<?php

namespace Stratis\Component\Migrator\Writer;
use Ddeboer\DataImport\Writer\AbstractWriter;
use Ddeboer\DataImport\Exception\WriterException;

/**
* Copy a file in one location to another
*/
class CopyFileWriter extends AbstractWriter
{
	/**
	* @var string
	*/
	protected $from = '/';
	protected $to 	= '/';
	
	/**
	* Constructor
	*
	* @param string $from
	* @param string $to
	*/
	public function __construct($from, $to)
	{
		if (! exec('which readlink')) {
			throw new WriterException('Readlink command is not available on this computer!');
		}
		
		// Use readlink to parse from/to path
		$this->from = exec('readlink -f ' . $from);
		$this->to 	= exec('readlink -f ' . $to);
		
		if (empty($this->from)) {
			throw new WriterException($this->from . ' path is not valid!');
		}
		
		if (empty($this->to)) {
			throw new WriterException($this->to . ' path is not valid!');
		}
	}
	
	/**
	* Copy file
	* Existing files will be overwritten
	*
	* @param array $item
	*/
	public function writeItem(array $item)
	{
		foreach ($item as $fileName) {
			
			$sourceFile = $this->from . DIRECTORY_SEPARATOR . $fileName;
			$destFile 	= $this->to . DIRECTORY_SEPARATOR . $fileName;
			
			if (file_exists($sourceFile)) {
				copy($sourceFile, $destFile);
			}
		}
	}
}