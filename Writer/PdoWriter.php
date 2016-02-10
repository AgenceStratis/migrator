<?php

namespace Stratis\Component\Migrator\Writer;
use Ddeboer\DataImport\Writer;
use Ddeboer\DataImport\Exception\WriterException;

// TODO: test if statement need to be refreshed (ex: Typo3MM)
class PdoWriter extends \Ddeboer\DataImport\Writer\PdoWriter
{
	/**
	* @param string $insertMode
	*/
	protected $insertMode = 'insert';
	
	/**
	* Constructor
	*
	* @param \PDO $pdo
	* @param string $tableName
	* @param string $insertMode
	*/
	public function __construct(\PDO $pdo, $tableName = null, $insertMode = '')
	{
		parent::__construct($pdo, $tableName);
		$this->insertMode = strtolower($insertMode);
	}
	
	/**
	* Create Statement using mode value
	* @param array $item
	*/
	protected function createStatement(array $item)
	{
		$values = ' (' . implode(',', array_keys($item)) . ') VALUES (' . substr(str_repeat('?,', count($item)), 0, -1) . ')';
		
		switch ($this->insertMode) {
			
			// insert values and reset it if primary key is matched
			case 'replace': {
				$statement = 'REPLACE INTO ' . $this->tableName . $values;
				break;
			}
			
			// classic insert, may cause errors if primary key is specified
			case 'insert':
			default: {
				$statement = 'INSERT INTO ' . $this->tableName . $values;
			}
		}
		
		return $statement;
	}
	
	/**
	* @param array 	$item
	*/
	public function writeItem(array $item)
	{
		try {
			
			// prepare the statment as soon as we know how many values there are
			// if (! $this->statement) {
				
				$this->statement = $this->pdo->prepare(
					$this->createStatement($item)
				);
				
				//for PDO objects that do not have exceptions enabled
				if (! $this->statement) {
					throw new WriterException('Failed to prepare write statement for item: ' . implode(',', $item));
				}
			// }

			//do the insert
			if (!$this->statement->execute(array_values($item))) {
				$this->pdo->errorInfo();
				throw new WriterException('Failed to write item: '.implode(',', $item));
			}

		} catch (\Exception $e) {
			//convert exception so the abstracton doesn't leak
			throw new WriterException('Write failed ('.$e->getMessage().').', null, $e);
		}
	}
}
