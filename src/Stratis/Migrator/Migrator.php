<?php

namespace Stratis\Migrator;

// use Stratis\Migrator\Config;
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

class Migrator
{
	protected $config;
	protected $logger;
	protected $workflow = null;
	
	public function __construct($file)
	{
		// setup logger
		$this->logger = new Logger('migrator');
		$this->logger->pushHandler(new StreamHandler('migrator.log', Logger::WARNING));
		
		if (! file_exists($file)) {
			return $this->logger->addError('YAML configuration file does not exists', array($file));
		}
		
		$this->config = new Config(Yaml::parse(file_get_contents($file)));
		
		// input / output
		$reader = $this->getReader();
		$writer = $this->getWriter();
	
		if ( $reader == null ) {
			return $this->logger->addError('Reader is not defined');
		}
	
		if ( $writer == null ) {
			return $this->logger->addError('Writer is not defined');
		}
		
		// workflow process
		$workflow = new Workflow($reader, $this->logger);
		$workflow->addWriter($writer);
		
		$converter = new Converter($this->config->get('processors'));
		$workflow->addItemConverter($converter);
		
		$workflow->process();
	}
	
	protected function getReader()
	{
		$reader = null;
		$type = $this->config->get('source', 'type');
		
		if ($type == 'csv') {
			$file = $this->config->get('source', 'options', 'file');
			$source = new \SplFileObject($file);
			$reader = new CsvReader($source);
			$reader->setHeaderRowNumber(0);
		}
		
		if ($type == 'sql') {
			$table = $this->config->get('source', 'options', 'table');
			$db = new medoo($this->config->get('source', 'options'));
			$reader = new PdoReader($db->pdo, 'SELECT * FROM ' . $table);
		}
		
		return $reader;
	}
	
	protected function getWriter()
	{
		$writer = null;
		$type = $this->config->get('dest', 'type');
		
		if ($type == 'csv') {
			$file = $this->config->get('dest', 'options', 'file');
			$header = $this->config->get('dest', 'options', 'fields');
			$writer = new CsvWriter();
			$writer->setStream(fopen($file, 'w'));
			
			if (count($header)) {
				$writer->writeItem( $header );
			}
		}
		
		if ( $type == 'sql' ) {
			$table = $this->config->get('dest', 'options', 'table');
			$db = new medoo($this->config->get('dest', 'options'));
			$writer = new PdoWriter($db->pdo, $table);
		}
		
		if (! $type) {
			$writer = new CallbackWriter( function($row) {
				var_dump($row);
			});
		}
		
		return $writer;
	}
}