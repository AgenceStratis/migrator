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
	/**
	* @var array
	*/
	protected $configuration;
	
	/**
	* @var Logger
	*/
	protected $logger;
	
	/**
	* Constructor
	*
	* @param string $fileName 
	* @param object $logger 
	*/
	public function __construct($fileName, $logger = null)
	{
		$options = array(
			
			'file' => '',
			
			 // CSV Options
			'header' => true,
			'delimiter' => ',',
			'enclosure' => '"',
			'utf8' => false,
			
			// JSON Options
			'pretty' => false,
			'convert_unicode' => false,
			
			// SQL Options
			'database_type' => 'mysql',
			'charset' => 'utf8',
			'server' => 'localhost',
			'database_name' => '',
			'username' => '',
			'password' => '',
			'table' => '',
			'query' => ''
		);
		
		$io = array(
			'type' => '',
			'options' => $options
		);
		
		$this->configuration = array(
			'source' => $io,
			'dest' => $io,
			'processors' => array(
				'values' => array(),
				'fields' => array()
			)
		);
		
		unset($options, $io);
		
		// add custom config file
		$this->loadConf($fileName);
		
		// create i/o parsers
		$reader = $this->getReader();
		$writer = $this->getWriter();
		
		// init workflow
		parent::__construct($reader, $logger);
		$this->addWriter($writer);
		
		// add processors to the workflow
		$this->addItemConverter(
			new Converter(
				$this->configuration['processors']
			)
		);
	}
	
	/**
	* Load configuration file
	*
	* @param string $fileName 
	*/
	protected function loadConf($fileName)
	{
		if (! file_exists($fileName)) {
			throw new \Exception('Configuration file does not exist');
		}
		
		$conf = Yaml::parse(
			file_get_contents($fileName)
		);
		
		$this->configuration = array_merge_recursive_distinct(
			$this->configuration, $conf
		);
	}
	
	/**
	* Get Reader
	* Create a reader object, according to local config
	*
	* @return object $reader
	*/
	protected function getReader()
	{
		$type 		= $this->configuration['source']['type'];
		$options 	= $this->configuration['source']['options'];
		
		switch ($type) {
			
			case 'csv': {
				
				$delimiter 	= $options['delimiter'];
				$file 		= $options['file'];
				$header 	= $options['header'];
				
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
				
				$file 	= $options['file'];
				
				$reader = new JsonReader(
					new \SplFileObject($file)
				);
				
				break;
			}
			
			case 'sql': {
				
				$table 	= $options['table'];
				$query 	= $options['query'];
				
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
		$type 		= $this->configuration['dest']['type'];
		$options 	= $this->configuration['dest']['options'];
		
		switch ($type) {
			
			case 'csv': {
				
				$delimiter 	= $options['delimiter'];
				$enclosure 	= $options['enclosure'];
				$file 		= $options['file'];
				$utf8 		= $options['utf8'];
				$header 	= $options['header'];
				
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
				
				$file 		= $options['file'];
				$pretty 	= $options['pretty'];
				$unicode 	= $options['convert_unicode'];
				
				$writer = new JsonWriter(
					fopen($file, 'w'),
					$pretty,
					$unicode
				);
				
				break;
			}
			
			case 'sql': {
				
				$table 	= $options['table'];
				
				$db 	= new medoo($options);
				$writer = new PdoWriter($db->pdo, $table);
				
				break;
			}
			
			case 'cli': {
				
				$writer = new CallbackWriter(function($row)
				{
					var_dump($row);
				});
				
				break;
			}
			
			default: {
				throw new \Exception('Writer is not defined');
			}
		}
		
		return $writer;
	}
}