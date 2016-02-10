<?php

namespace Stratis\Component\Migrator\Writer;
use Ddeboer\DataImport\Writer\PdoWriter;
use \Ddeboer\DataImport\Exception\WriterException;

class Typo3Writer extends PdoWriter
{
	protected $mmTables = array();
	
	/**
	* @param \PDO   $pdo
	* @param string $tableName
	* @param array 	$mmTables
	*/
	public function __construct(\PDO $pdo, $tableName, $mmTables = array())
	{
		parent::__construct($pdo, $tableName);
		$this->mmTables = $mmTables;
	}
	
	public function writeItem(array $item)
	{
		if (is_array($this->mmTables)) {
			foreach ($this->mmTables as $mmTable => $uidKey) {
				
				if (! array_key_exists('uid', $item) ||
					! array_key_exists($uidKey, $item)) {
						
					throw new WriterException("MM: The key (" . $uidKey . ") doesn't exist in " . implode(',', $item));
				}
				
				$this->writeMM($mmTable, $item['uid'], $item[$uidKey]);
				
				// remove key (need to be fixed)
				$item['uid'] = null;
				unset($item[$uidKey]);
			}
		}
		
		// var_dump(array_keys($item));
		// die;
		// parent::writeItem($item);
	}
	
	public function writeMM($tableName, $uidLocal, $uidForeign)
	{
		// build up an item
		$item = array(
			'uid_local' => $uidLocal,
			'uid_foreign' => $uidForeign
		);
		
		try {
			
			//prepare the statment as soon as we know how many values there are
			if (! $this->statement) {
				
				$this->statement = $this->pdo->prepare(
					'INSERT INTO ' . $tableName . '(' . implode(',', array_keys($item)) . ') VALUES (' . substr(str_repeat('?,', count($item)), 0, -1) . ')'
				);
				
				// var_dump($item); die;
				
				//for PDO objects that do not have exceptions enabled
				if (! $this->statement) {
					throw new WriterException('Failed to prepare write statement for item: ' . implode(',', $item));
				}
			}
			
			//do the insert
			if (! $this->statement->execute(array_values($item))) {
				throw new WriterException('Failed to write item: ' . implode(',', $item));
			}
			
		} catch (\Exception $e) {
			//convert exception so the abstracton doesn't leak
			throw new WriterException('Write failed (' . $e->getMessage() . ').', null, $e);
		}
	}
}