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
	protected $confData;
	protected $logger;
	protected $workflow = null;
	
	public function __construct($file, $logger = null)
	{
		// echo $file;
		// die;
		// parent::
		
		// setup logger
		// $this->logger = new Logger('migrator');
		// $this->logger->pushHandler(new StreamHandler('migrator.log', Logger::WARNING));
		
		if (! file_exists($file)) {
			return;
		}
		
		// get YAML config file data
		$this->loadConf($file);
		
		// input / output
		$reader = $this->getReader();
		$writer = $this->getWriter();
		
		// build workflow
		if ($logger !== null) {
			parent::__construct($reader, $logger);
		} else {
			parent::__construct($reader);
		}
		
		// add writer
		$this->addWriter($writer);
		
		// add processors
		$converter = new Converter($this->getConf('processors'));
		$this->addItemConverter($converter);
	}
	
	protected function loadConf($file)
	{
		$fileConf = Yaml::parse(file_get_contents($file));
		
		$options = array('file', 'fields' => array(), 'database_type',
			'database_name', 'server', 'username', 'password', 'charset', 'table');
		
		$baseConf = array(
			'source' => array('type', 'options' => $options),
			'dest' => array('type', 'options' => $options),
			'processors' => array('values' => array(), 'fields' => array()));
		
		$this->confData = array_merge($baseConf, $fileConf);
	}
	
	public function getConf()
	{
		if (func_num_args() == 0) {
			return $this->confData;
		}
		
		$search = $this->confData;
		
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
				$reader = null;
			}
		}
		
		return $reader;
	}
	
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
				$writer = new CallbackWriter(function($row) {
					var_dump($row);
				});
			}
		}
		
		return $writer;
	}
}