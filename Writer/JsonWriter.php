<?php

namespace Stratis\Component\Migrator\Writer;
use Ddeboer\DataImport\Writer\AbstractStreamWriter;

/**
* Write to a JSON file
*/
class JsonWriter extends AbstractStreamWriter
{
	/**
	* @var integer
	*/
	private $count = 0;
	
	/**
	* @var boolean
	*/
	private $pretty = false;
	private $convert_unicode = false;
	
	/**
	* Create ce JSON_ENCODE option bitmask
	*/
	private function options()
	{
		$options = 0;
		
		if ($this->pretty) {
			$options |= JSON_PRETTY_PRINT;
		}
		
		if ($this->convert_unicode) {
			$options |= JSON_UNESCAPED_UNICODE;
		}
		
		return $options;
	}
	
	/**
	* Constructor
	*
	* @param boolean $pretty
	*/
	public function __construct($pretty = false, $unicode = false)
	{
		parent::__construct();
		
		$this->pretty = $pretty;
		$this->convert_unicode = $unicode;
	}
	
	/**
	* Open an array
	*/
	public function prepare()
	{
		fwrite($this->getStream(), "[");
	}
	
	/**
	* Encode items to JSON and append to the file
	*
	* @param array $item
	*/
	public function writeItem(array $item)
	{
		if ($this->count++ > 0) {
			fwrite($this->getStream(), ",\n");
		}
		
		fwrite($this->getStream(), json_encode($item, $this->options()));
	}
	
	/**
	* End of the file
	*/
	public function finish()
	{
		fwrite($this->getStream(), "]\n");
	}
}