<?php

namespace Stratis\Component\Migrator;

use medoo;
use Symfony\Component\Yaml\Yaml;
use Ddeboer\DataImport\Workflow;

// Readers
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\PdoReader;
use Stratis\Component\Migrator\Reader\JsonReader;

// Writers
use Ddeboer\DataImport\Writer\CsvWriter;
use Ddeboer\DataImport\Writer\PdoWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Stratis\Component\Migrator\Writer\JsonWriter;

/**
* Stratis Migrator
* Importer/Exporter for multiples data sources
* 
* Usage:
* 	$migrator = new Migrator('config.yaml');
* 	$migrator->process();
*/
class Migrator extends Workflow
{
	protected $configuration;
	protected $logger;
	protected $workflow = null;
	
	/**
	* Constructor
	*
	* @param string $file 
	* @param object $logger 
	*/
	public function __construct($file, $logger = null)
	{
		$options = array(
			'file' => '',
			
			 // CSV Options
			'header' => true, 'fields' => array(), 'delimiter' => ',', 'enclosure' => '"', 'utf8' => false,
			
			// JSON Options
			'pretty' => false, 'convert_unicode' => false,
			
			// SQL Options
			'database_type' => 'mysql', 'charset' => 'utf8', 'server' => 'localhost',
			'database_name' => '', 'username' => '', 'password' => '', 'table' => '',
			'query' => ''
		);
		
		$this->configuration = array(
			'source' => array('type' => '', 'options' => $options),
			'dest' => array('type' => '', 'options' => $options),
			'processors' => array('values' => array(), 'fields' => array()));
		
		// add custom config file
		$this->loadConf($file);
		
		// create i/o parsers
		$reader = $this->getReader();
		$writer = $this->getWriter();
		
		// init workflow
		parent::__construct($reader, $logger);
		$this->addWriter($writer);
		
		$converter = new Converter($this->getConf('processors'));
		$this->addItemConverter($converter);
	}
	
	/**
	* Load configuration file
	*
	* @param string $file 
	*/
	protected function loadConf($file)
	{
		if (! file_exists($file)) {
			throw new \Exception('Specified config file does not exist');
		}
		
		$conf = Yaml::parse(file_get_contents($file));
		$this->configuration = array_merge_recursive_distinct(
			$this->configuration, $conf
		);
	}
	
	/**
	* Get configuration
	* Crawl $configuration and get a specific conf value
	*
	* example: $this->getConf('source', 'options', 'file');
	*
	* @params multiple strings
	* @return string $search
	*/
	public function getConf()
	{
		if (func_num_args() == 0) {
			return $this->configuration;
		}
		
		$search = $this->configuration;
		
		foreach (func_get_args() as $arg) {
			if (array_key_exists($arg, $search)) {
				$search = $search[ $arg ];
			} else {
				$search = null;
				break;
			}
		}
		
		return $search;
	}
	
	/**
	* Get Reader
	* Create a reader object, according to local config
	*
	* @return object $reader
	*/
	protected function getReader()
	{
		$type = $this->getConf('source', 'type');
		
		switch ($type) {
			
			case 'csv': {
				
				$delimiter 	= $this->getConf('source', 'options', 'delimiter');
				$file 		= $this->getConf('source', 'options', 'file');
				$header 	= $this->getConf('source', 'options', 'header');
				
				$reader = new CsvReader(
					new \SplFileObject($file),
					$delimiter
				);
				
				if ($header) {
					$reader->setHeaderRowNumber(0);
				}
				
				break;
			}
			
			case 'json': {
				
				$file 	= $this->getConf('source', 'options', 'file');
				
				$reader = new JsonReader(
					new \SplFileObject($file)
				);
				
				break;
			}
			
			case 'sql': {
				
				$options 	= $this->getConf('source', 'options');
				$table 		= $this->getConf('source', 'options', 'table');
				$query 		= $this->getConf('source', 'options', 'query');
				
				if (strlen($table) == 0) {
					throw new \Exception('Table is not defined');
				}
				
				if (strlen($query) == 0) {
					$query = 'SELECT * FROM ' . $table;
				}
				
				$db 	= new medoo($options);
				$reader = new PdoReader($db->pdo, $query);
				
				break;
			}
			
			default: {
				throw new \Exception('Reader is not defined');
			}
		}
		
		return $reader;
	}
	
	/**
	* Get Writer
	* Create a writer object, according to local config
	*
	* @return object $writer
	*/
	protected function getWriter()
	{
		$type = $this->getConf('dest', 'type');
		
		switch ($type) {
			
			case 'csv': {
				
				$delimiter 	= $this->getConf('dest', 'options', 'delimiter');
				$enclosure 	= $this->getConf('dest', 'options', 'enclosure');
				$file 		= $this->getConf('dest', 'options', 'file');
				$utf8 		= $this->getConf('dest', 'options', 'utf8');
				$header 	= $this->getConf('dest', 'options', 'header');
				
				$writer = new CsvWriter(
					$delimiter,
					$enclosure,
					fopen($file, 'w'),
					$utf8,
					true
				);
				
				break;
			}
			
			case 'json': {
				
				$file 		= $this->getConf('dest', 'options', 'file');
				$pretty 	= $this->getConf('dest', 'options', 'pretty');
				$unicode 	= $this->getConf('dest', 'options', 'convert_unicode');
				
				$writer = new JsonWriter($pretty, $unicode);
				$writer->setStream(fopen($file, 'w'));
				
				break;
			}
			
			case 'sql': {
				
				$options 	= $this->getConf('dest', 'options');
				$table 		= $this->getConf('dest', 'options', 'table');
				
				$db 	= new medoo($options);
				$writer = new PdoWriter($db->pdo, $table);
				
				break;
			}
			
			default: {
				// throw new \Exception('Writer is not defined');
				$writer = new CallbackWriter(function($row) {
					var_dump($row);
				});
			}
		}
		
		return $writer;
	}
}