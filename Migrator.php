<?php

namespace Stratis\Component\Migrator;

use medoo;
use Symfony\Component\Yaml\Yaml;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Filter\OffsetFilter;

// Readers
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\PdoReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use Stratis\Component\Migrator\Reader\JsonReader;
use Stratis\Component\Migrator\Reader\MMReader;

// Writers
use Ddeboer\DataImport\Writer\CsvWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Stratis\Component\Migrator\Writer\PdoWriter;
use Stratis\Component\Migrator\Writer\JsonWriter;
use Stratis\Component\Migrator\Writer\Typo3MMWriter;
use Stratis\Component\Migrator\Writer\CopyFileWriter;
use Stratis\Component\Migrator\Writer\Typo3FileWriter;

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
	* Constructor
	*
	* @param string $fileName 
	* @param object $logger 
	*/
	public function __construct($fileName, $logger = null)
	{
		$options = array(
			
			'file' 		=> '',
			'offset' 	=> 0,
			'count' 	=> null,
			
			 // CSV
			'header' 	=> true,
			'delimiter' => ',',
			'enclosure' => '"',
			'utf8' 		=> false,
			
			// JSON
			'pretty' 	=> false,
			'unicode' 	=> false,
			
			// SQL
			'database_type' => 'mysql',
			'charset' => 'utf8',
			'server' => 'localhost',
			'database_name' => '',
			'username' => '',
			'password' => '',
			'table' => '',
			'query' => '',
			'insert_mode' => 'insert',
			
			// Typo3 MM
			'mm_tables' => array(),
			
			// Copy File
			'from' 	=> '',
			'to' 	=> '',
			
			// Typo3 File
			'root_dir' => array(),
			'file_dir' => array(),
			
			// Excel
			'sheet' => null,
			
			// MM
			'local' 	=> array(),
			'foreign' 	=> array(),
			'rule' 		=> array(),
		);
		
		$io = array(
			'type' => '',
			'options' => $options
		);
		
		$this->configuration = array(
			
			'require' 	=> array(),
			
			'source' 	=> $io,
			'dest' 		=> $io,
			
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
		
		// offset + count
		$this->addFilter(
			new OffsetFilter(
				$this->configuration['source']['options']['offset'],
				$this->configuration['source']['options']['count']
			)
		);
		
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
	* @param mixed $fileName
	*/
	protected function loadConf($fileName)
	{
		if (is_array($fileName)) {
			
			foreach ($fileName as $subFile) {
				$this->loadConf($subFile);
			}
			
			return;
		}
		
		if (! file_exists($fileName)) {
			throw new \Exception('Configuration file does not exist');
		}
		
		$conf = Yaml::parse(
			file_get_contents($fileName)
		);
		
		// execute requires
		if (array_key_exists('require', $conf)) {
			
			$require = $conf['require'];
			$reqPath = dirname($fileName) . '/';
			
			if (is_array($require) && count($require) > 0) {
				foreach ($require as $reqFile) {
					$this->loadConf($reqPath . $reqFile);
				}
			}
			
			if (is_string($require) && strlen($require) > 0) {
				$this->loadConf($reqPath . $require);
			}
		}
		
		// merge configurations
		$this->configuration = array_merge_recursive_distinct(
			$this->configuration, $conf
		);
	}
	
	/**
	* Get Reader
	* Create a reader object, according to local config
	*
	* @param array $source
	* @return object $reader
	*/
	protected function getReader($source = null)
	{
		if ($source == null) {
			
			$type 		= $this->configuration['source']['type'];
			$options 	= $this->configuration['source']['options'];
			
		} else {
			
			$type 		= $source['type'];
			$options 	= $source['options'];
		}
		
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
			
			case 'excel': {
				
				$file 	= $options['file'];
				$header = $options['header'];
				$sheet  = $options['sheet'];
				
				$reader = new ExcelReader(
					new \SplFileObject($file),
					($header ? 0 : null),
					$sheet
				);
				
				break;
			}
			
			case 'mm': {
				
				$local 		= $options['local'];
				$foreign 	= $options['foreign'];
				$rule 		= $options['rule'];
				
				$reader = new MMReader(
					$this->getReader($local),
					$this->getReader($foreign),
					$rule
				);
				
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
				$unicode 	= $options['unicode'];
				
				$writer = new JsonWriter(
					fopen($file, 'w'),
					$pretty,
					$unicode
				);
				
				break;
			}
			
			case 'sql': {
				
				$table 		= $options['table'];
				$insertMode = $options['insert_mode'];
				
				$db 	= new medoo($options);
				$writer = new PdoWriter(
					$db->pdo,
					$table,
					$insertMode
				);
				
				break;
			}
			
			case 'typo3mm': {
				
				$mmTables 	= $options['mm_tables'];
				$insertMode = $options['insert_mode'];
				
				$db 	= new medoo($options);
				$writer = new Typo3MMWriter(
					$db->pdo,
					$mmTables,
					$insertMode
				);
				
				break;
			}
			
			case 'typo3file': {
				
				$realDir 	= $options['real_dir'];
				$fileDir 	= $options['file_dir'];
				$insertMode = $options['insert_mode'];
				
				$db 	= new medoo($options);
				$writer = new Typo3FileWriter(
					$db->pdo,
					$realDir,
					$fileDir,
					$insertMode
				);
				
				break;
			}
			
			case 'copyfile': {
				
				$writer = new CopyFileWriter(
					$options['from'],
					$options['to']
				);
				
				break;
			}
			
			case 'cli': {
				
				$writer = new CallbackWriter(function($item)
				{
					var_dump($item);
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
