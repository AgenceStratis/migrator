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
	* JSON ouput will be formated
	* @var boolean
	*/
	private $pretty = false;
	
	/**
	* Auto conversion to unicode characters
	* @var boolean
	*/
	private $unicode = false;
	
	/**
	* Create ce JSON_ENCODE option bitmask
	*/
	private function options()
	{
		$options = 0;
		
		if ($this->pretty) {
			$options |= JSON_PRETTY_PRINT;
		}
		
		if (! $this->unicode) {
			$options |= JSON_UNESCAPED_UNICODE;
		}
		
		return $options;
	}
	
	/**
	* Constructor
	*
	* @param object $stream
	* @param boolean $pretty
	* @param boolean $unicode
	*/
	public function __construct($stream = null, $pretty = false, $unicode = false)
	{
		parent::__construct($stream);
		
		$this->pretty = $pretty;
		$this->unicode = $unicode;
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
		
		fwrite(
			$this->getStream(),
			json_encode($item, $this->options())
		);
	}
	
	/**
	* End of the file
	*/
	public function finish()
	{
		fwrite($this->getStream(), "]\n");
	}
}