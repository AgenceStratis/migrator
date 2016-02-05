<?php

namespace Stratis\Component\Migrator;

use medoo;
use Symfony\Component\Yaml\Yaml;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;

// Readers
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\PdoReader;
use Stratis\Component\Migrator\Reader\JsonReader;

// Writers
use Ddeboer\DataImport\Writer\CsvWriter;
use Ddeboer\DataImport\Writer\PdoWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Stratis\Component\Migrator\Writer\JsonWriter;

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
		$options = array('file', 'fields' => array(), 'database_type',
			'database_name', 'server', 'username', 'password', 'charset', 'table');
		
		$this->configuration = array(
			'source' => array('type', 'options' => $options),
			'dest' => array('type', 'options' => $options),
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
		$this->configuration = array_merge($this->configuration, $conf);
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
				$file = $this->getConf('source', 'options', 'file');
				$source = new \SplFileObject($file);
				$reader = new CsvReader($source);
				$reader->setHeaderRowNumber(0);
				break;
			}
			
			case 'json': {
				$file = $this->getConf('source', 'options', 'file');
				$source = new \SplFileObject($file);
				$reader = new JsonReader($source);
				break;
			}
			
			case 'pdo': {
				$table = $this->getConf('source', 'options', 'table');
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
				$file = $this->getConf('dest', 'options', 'file');
				$header = $this->getConf('dest', 'options', 'fields');
				$writer = new CsvWriter();
				$writer->setStream(fopen($file, 'w'));
				
				if (count($header)) {
					$writer->writeItem( $header );
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