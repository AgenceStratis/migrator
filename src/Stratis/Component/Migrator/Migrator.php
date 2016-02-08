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
*
* @param string $file 
* @param string $logger 
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
	* @param string $logger 
	*/
	public function __construct($file, $logger = null)
	{
		// init configuration data
		$options = array(
			'file' => '',
			'header' => true, 'fields' => array(), 'delimiter' => ',',
			'database_type' => 'mysql', 'charset' => 'utf8', 'server' => 'localhost',
			'database_name' => '', 'username' => '', 'password' => '', 'table' => '');
		
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
		if ($logger !== null) {
			parent::__construct($reader, $logger);
		} else {
			parent::__construct($reader);
		}
		
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
	*
	* Crawl $configuration and get a specific conf value
	* example: $this->getConf('source', 'options', 'file');
	*
	* @params multiple strings
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
	* Create reader object, according to config
	*/
	protected function getReader()
	{
		$type = $this->getConf('source', 'type');
		
		switch ($type) {
			
			case 'csv': {
				
				$delimiter = $this->getConf('source', 'options', 'delimiter');
				$file = $this->getConf('source', 'options', 'file');
				
				$source = new \SplFileObject($file);
				$reader = new CsvReader($source, $delimiter);
				
				if ($this->getConf('source', 'options', 'header')) {
					$reader->setHeaderRowNumber(0);
				}
				
				break;
			}
			
			case 'json': {
				$file = $this->getConf('source', 'options', 'file');
				$source = new \SplFileObject($file);
				$reader = new JsonReader($source);
				break;
			}
			
			case 'sql': {
				$table = $this->getConf('source', 'options', 'table');
				if (! strlen($table)) {
					throw new \Exception('Table is not defined');
				}
				
				$db = new medoo($this->getConf('source', 'options'));
				$reader = new PdoReader($db->pdo, 'SELECT * FROM ' . $table);
				break;
			}
			
			default: {
				throw new \Exception('Reader is not defined');
			}
		}
		
		return $reader;
	}
	
	/**
	* Create writer object, according to config
	*/
	protected function getWriter()
	{
		$type = $this->getConf('dest', 'type');
		
		switch ($type) {
			
			case 'csv': {
				
				$delimiter = $this->getConf('dest', 'options', 'delimiter');
				$file = $this->getConf('dest', 'options', 'file');
				
				$writer = new CsvWriter( $delimiter );
				$writer->setStream(fopen($file, 'w'));
				
				$header = $this->getConf('dest', 'options', 'fields');
				if (count($header)) {
					$writer->writeItem($header);
				}
				
				break;
			}
			
			case 'json': {
				$file = $this->getConf('dest', 'options', 'file');
				$writer = new JsonWriter();
				$writer->setStream(fopen($file, 'w'));
				break;
			}
			
			case 'sql': {
				$table = $this->getConf('dest', 'options', 'table');
				$db = new medoo($this->getConf('dest', 'options'));
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