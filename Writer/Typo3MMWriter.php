<?php

namespace Stratis\Component\Migrator\Writer;
use Ddeboer\DataImport\Writer\PdoWriter;
use Ddeboer\DataImport\Exception\WriterException;

// TODO: create an extended PdoWriter
// TODO: insert unique values
class Typo3MMWriter extends PdoWriter
{
	/**
	* @var array
	*/
	protected $mmTables = array();
	
	/**
	* @param \PDO   $pdo
	* @param array 	$mmTables
	*/
	public function __construct(\PDO $pdo, $mmTables)
	{
		parent::__construct($pdo, null);
		$this->mmTables = $mmTables;
	}
	
	/**
	* @param array 	$item
	*/
	public function writeItem(array $item)
	{
		if (is_array($this->mmTables)) {
			foreach ($this->mmTables as $mmTable => $uidKey) {
				
				if (! array_key_exists('uid', $item) ||
					! array_key_exists($uidKey, $item)) {
						
					throw new WriterException("MM: The key (" . $uidKey . ") doesn't exist in " . implode(',', $item));
				}
				
				// set table name
				$this->tableName = $mmTable;
				
				// insert mm record
				parent::writeItem(array(
					'uid_local' 	=> $item['uid'],
					'uid_foreign' 	=> $item[$uidKey]
				));
			}
		}
	}
}