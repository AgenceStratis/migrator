<?php

namespace Stratis\Component\Migrator\Reader;
use \Ddeboer\DataImport\Workflow;
use \Ddeboer\DataImport\Reader\ReaderInterface;
use \Ddeboer\DataImport\Writer\CallbackWriter;

/**
* Many To Many Reader is a component for using multiple sources and create relations
* Writer must be compatible with this type of data source
*/
class MMReader implements ReaderInterface
{
	/**
	* @var array
	*/
	protected $data 	= array();
	protected $local 	= array();
	protected $foreign 	= array();
	
	/**
	* Get Reader Data
	* Execute a Workflow to gather data from any reader
	*
	* @param Reader $reader
	* @return array $data
	*/
	protected function getReaderData($reader)
	{
		$data = array();
		
		$writer = new CallbackWriter(function ($item) use(&$data) {
			array_push($data, $item);
		});
		
		$workflow = new Workflow($reader);
		$workflow->addWriter($writer);
		$workflow->process();
		
		return $data;
	}
	
	/**
	* Constructor
	*
	* @param Reader $localReader
	* @param Reader $foreignReader
	* @param array $rule
	*/
	public function __construct($localReader, $foreignReader, $rule)
	{
		$this->local 	= $this->getReaderData($localReader);
		$this->foreign 	= $this->getReaderData($foreignReader);
		
		// check if rules exists
		if (count($rule) !== 2) {
			throw new \Exception("MM: Rule is not defined !");
		}
		
		// references to compare (default should be uid)
		$localRef 	= $rule[0];
		$foreignRef = $rule[1];
		
		// var_dump($localRef, $this->local[0]); die;
		// var_dump($foreignRef, $this->foreign[0]); die;
		
		// check if references exist
		if (! array_key_exists($localRef, $this->local[0])) {
			throw new \Exception("MM: Local Key does not exist in this dataset !");
		}
		
		if (! array_key_exists($foreignRef, $this->foreign[0])) {
			throw new \Exception("MM: Foreign Key does not exist in this dataset !");
		}
		
		// build routes like [foreignPrimaryKey => foreignRef] array
		$refs = array();
		
		foreach ($this->foreign as $n => $item) {
			
			// get primary key from foreign item
			// get auto increment if it does not exists
			// TODO: custom primary keys other than [uid]
			$key = array_key_exists('uid', $item) ? $item['uid'] : $n;
			
			// add route to reference
			$refs[$key] = $item[$foreignRef];
		}
		
		// var_dump($refs); die;
		
		// crawl local data
		foreach ($this->local as $n => $item) {
			
			// get primary key from local item
			// get auto increment if it does not exists
			// TODO: custom primary keys other than [uid]
			$key = array_key_exists('uid', $item) ? $item['uid'] : $n;
			
			// search for foreign primary key
			$fPKey = array_search($item[$localRef], $refs);
			
			// foreign ref value found
			// add the mm item to the data list
			if ($fPKey !== false) {
				
				$this->data[] = array(
					'uid_local' => $key,
					'uid_foreign' => $fPKey
				);
			}
		}
		
		// var_dump($this->data); die;
	}
	
	/**
	* Load data if it hasn't been loaded yet
	*/
	protected function loadData()
	{
		// if (null === $this->data) {
		// 	$this->statement->execute();
		// 	$this->data = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
		// }
	}
	
	public function getFields()
	{
		return array();
	}
	
	public function current()
	{
		return current($this->data);
	}
	
	/**
	* {@inheritdoc}
	*/
	public function next()
	{
		next($this->data);
	}
	
	/**
	* {@inheritdoc}
	*/
	public function key()
	{
		return key($this->data);
	}
	
	/**
	* {@inheritdoc}
	*/
	public function valid()
	{
		$key = key($this->data);
		return ($key !== null && $key !== false);
	}
	
	/**
	* {@inheritdoc}
	*/
	public function rewind()
	{
		// $this->loadData();
		reset($this->data);
	}
	
	/**
	* {@inheritdoc}
	*/
	public function count()
	{
		// $this->loadData();
		return count($this->data);
	}
}
