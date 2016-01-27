<?php

// require __DIR__ . 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

use Ddeboer\DataImport\Workflow;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\PdoReader;

use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CsvWriter;
use Ddeboer\DataImport\Writer\PdoWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;

use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;

// todo:
// - implements workflow
// - common converters
// - use converters in loop (use order -> supported)
// - [bug] no header in csvwriter
// - [warn] no processors field/value given
// - each args = new converter

class Converter {
	
	protected $config = array();
	protected $workflow = null;
	
	public function __construct ( $conf ) {
		$this->loadConf( $conf );
	}
	
	public function loadConf ( $conf ) {
		
		if ( file_exists( $conf )) {
			
			$this->config = Yaml::parse( file_get_contents( $conf ));
			
			$reader = $this->getReader();
			$writer = $this->getWriter();
		
			if ( $reader !== null && $writer !== null ) {
				
				$this->workflow = new Workflow( $reader );
				$this->workflow->addWriter( $writer );
			}
			
			$this->parseProcessors();
		}
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
			// var_dump( $reader->getFields() );
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
			$writer->writeItem( $header );
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
	
	protected function parseProcessors () {
		
		if ( $this->workflow == null ) return;
		
		$converter = new CallbackItemConverter( function ( $item ) {
			
			$oldItem = $item;
			
			// VALUES
			
			foreach ( $this->config['processors']['values'] as $field => $params ) {
				
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
			
			// ROUTE INIT
			
			$route = array();
			
			foreach ( $oldItem as $key => $v ) {
				
				$route[ $key ] = $key;
			}
			
			// FIELDS
			
			foreach ( $this->config['processors']['fields'] as $field => $params ) {
				
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
					// var_dump( $params );
				}
			}
			
			// ROUTE APPLY
			
			$routedItem = array();
			
			foreach ( $route as $baseField => $targetField ) {
				$routedItem[ $targetField ] = $item[ $baseField ];
			}
			
			// return $item;
			return $routedItem;
		});
		
		$this->workflow->addItemConverter( $converter );
	}
	
	public function run () {
		
		if ( $this->workflow !== null ) {
			
			$this->workflow->process();
		}
	}
}