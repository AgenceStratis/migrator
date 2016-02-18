<?php

namespace Stratis\Component\Migrator\Writer;
use Stratis\Component\Migrator\Writer\PdoWriter;
use Ddeboer\DataImport\Exception\WriterException;

class Typo3FileWriter extends PdoWriter
{
	/**
	* @var array
	*/
	protected $mmTables = array();
	
	/**
	* Cosntructor
	*
	* @param \PDO $pdo
	* @param string $insertMode
	*/
	public function __construct(\PDO $pdo, $insertMode)
	{
		parent::__construct($pdo, 'sys_file', $insertMode);
	}
	
	/**
	* @param array 	$item
	*/
	public function writeItem(array $item)
	{
		
		
		// if (is_array($this->mmTables)) {
		// 	foreach ($this->mmTables as $mmTable => $uidKey) {
				
		// 		if (! array_key_exists('uid', $item) ||
		// 			! array_key_exists($uidKey, $item)) {
						
		// 			throw new WriterException("MM: The key (" . $uidKey . ") doesn't exist in " . implode(',', $item));
		// 		}
				
		// 		// set table name
		// 		$this->tableName = $mmTable;
				
		// 		// insert mm record
		// 		parent::writeItem(array(
		// 			'uid_local' 	=> $item['uid'],
		// 			'uid_foreign' 	=> $item[$uidKey],
		// 			'sorting' 		=> 1
		// 		));
		// 	}
		// }
		print_r($item);
	}
}