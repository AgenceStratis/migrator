<?php

namespace Stratis;

use medoo;

use Symfony\Component\Yaml\Yaml;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\PdoReader;
use Ddeboer\DataImport\Writer\CsvWriter;
use Ddeboer\DataImport\Writer\PdoWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class Config {
	
	protected $data = array();
	
	public function set ( $data ) { }
	
	public function get () { }
	
	public function has () { }
}


class Converter implements ItemConverterInterface {
	
	protected $processors = array();
	
	// rulesValues = array( 'round', 'toInteger' )...
	// rulesFields = array( 'upperCase', 'lowerCase' )...
	
	public function __construct ( $processors ) {
		
		$this->processors = $processors;
	}
	
	protected function processValues ( &$item ) {
		
		foreach ( $this->processors['values'] as $field => $params ) {
			
			// params = array (multiples functions)
			if ( is_array( $params ) && count( $params > 0 )) {
				
				$value = $item[ $field ];
			
				if ( in_array( 'upperCase', $params )) {
					$value = strtoupper( $value );
				}
			
				if ( in_array( 'stripTags', $params )) {
					$value = strip_tags( $value );
				}
			
				if ( in_array( 'round', $params )) {
					$value = $value | 0;
				}
				
				$item[ $field ] = $value;
			}
			
			// params = string (assign data)
			if ( is_string( $params )) {
				
				$item[ $field ] = $params;
			}
		}
	}
	
	protected function route ( $item ) {
		
		$route = array();
		
		foreach ( $item as $key => $v ) {
			
			$route[ $key ] = $key;
		}
		
		return $route;
	}
	
	protected function processFields ( &$item, &$route ) {
		
		foreach ( $this->processors['fields'] as $field => $params ) {
			
			// params = array (multiples functions)
			if ( is_array( $params ) && count( $params > 0 )) {
				
				$newKey = $field;
				
				if ( in_array( 'lowerCase', $params )) {
					$newKey = strtolower( $newKey );
				}
				
				if ( in_array( 'upperCase', $params )) {
					$newKey = strtoupper( $newKey );
				}
			
				if ( in_array( 'stripTags', $params )) {
					$newKey = strip_tags( $newKey );
				}
				
				$item[ $newKey ] = $item[ $field ];
				unset( $item[ $field ]);
			}
			
			// params = string (assign data)
			if ( is_string( $params )) {
				// array_key_exists( $params, $item )
				$route[ $field ] = $params;
			}
		}
	}
	
	protected function processRoute ( $item, $route ) {
		
		$routedItem = array();
		
		foreach ( $route as $baseField => $targetField ) {
			
			$routedItem[ $targetField ] = $item[ $baseField ];
		}
		
		return $routedItem;
	}
	
	public function convert( $item ) {
		
		$route = $this->route( $item );
		
		if ( array_key_exists( 'values', $this->processors )) {
			$this->processValues( $item );
		}
		
		if ( array_key_exists( 'fields', $this->processors )) {
			$this->processFields( $item, $route );
		}
		
		return $this->processRoute( $item, $route );
	}
}

class Migrator {
	
	protected $config;
	protected $logger;
	protected $workflow = null;
	
	public function __construct ( $file ) {
		
		// setup logger
		$this->logger = new Logger( 'migrator' );
		$this->logger->pushHandler( new StreamHandler( 'migrator.log', Logger::WARNING ));
		
		if ( ! file_exists( $file )) {
			return $this->logger->addError( 'YAML configuration file does not exists', array( $file ));
		}
		
		$this->config = Yaml::parse( file_get_contents( $file ));
		
		// input / output
		$reader = $this->getReader();
		$writer = $this->getWriter();
	
		if ( $reader == null ) {
			return $this->logger->addError( 'Reader is not defined' );
		}
	
		if ( $writer == null ) {
			return $this->logger->addError( 'Writer is not defined' );
		}
		
		// workflow process
		$workflow = new Workflow( $reader, $this->logger );
		$workflow->addWriter( $writer );
		
		if ( array_key_exists( 'processors', $this->config )) {
			
			$converter = new Converter( $this->config['processors'] );
			$workflow->addItemConverter( $converter );
		}
		
		$workflow->process();
	}
	
	protected function getReader () {
		
		$reader = null;
		$type = $this->config['source']['type'];
		
		if ( $type == 'csv' ) {
			
			$file = $this->config['source']['options']['file'];
			$source = new \SplFileObject( $file );
			$reader = new CsvReader( $source );
			$reader->setHeaderRowNumber( 0 );
		}
		
		if ( $type == 'sql' ) {
			
			$params = $this->config['source']['options'];
			$table = $this->config['source']['options']['table'];
			
			$db = new medoo( $params );
			$reader = new PdoReader( $db->pdo, 'SELECT * FROM ' . $table );
		}
		
		return $reader;
	}
	
	protected function getWriter () {
		
		$writer = null;
		$type = $this->config['dest']['type'];
		
		if ( $type == 'csv' ) {
			
			$file = $this->config['dest']['options']['file'];
			$header = $this->config['dest']['options']['fields'];
			
			$writer = new CsvWriter();
			$writer->setStream( fopen( $file, 'w' ));
			// $writer->writeItem( $header );
		}
		
		if ( $type == 'sql' ) {
			
			$params = $this->config['dest']['options'];
			$table = $this->config['dest']['options']['table'];
			
			$db = new medoo( $params );
			$writer = new PdoWriter( $db->pdo, $table );
		}
		
		// console logs
		// $writer = new CallbackWriter( function ( $row ) {
		// 	var_dump( $row );
		// });
		
		return $writer;
	}
	
	public function itemConverter () {
		
		return  function ( $item ) {

		};
	}
}