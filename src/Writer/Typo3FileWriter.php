<?php

namespace Stratis\Component\Migrator\Writer;
use Stratis\Component\Migrator\Writer\PdoWriter;
use Ddeboer\DataImport\Exception\WriterException;

/**
* Add DB entries for files, related to news
*/
class Typo3FileWriter extends PdoWriter
{
	/**
	* @var string
	*/
	protected $realDir = '/';
	protected $fileDir = '/';
	
	/**
	* Cosntructor
	*
	* @param \PDO $pdo
	* @param string $realDir
	* @param string $fileDir
	* @param string $insertMode
	*/
	public function __construct(\PDO $pdo, $realDir, $fileDir, $insertMode)
	{
		parent::__construct($pdo, null, $insertMode);
		
		if (! exec('which readlink')) {
			throw new WriterException('Readlink command is not available on this computer!');
		}
		
		// Use readlink to parse path
		$this->realDir = exec('readlink -f ' . $realDir);
		$this->fileDir = $fileDir;
		
		if (! is_dir($this->realDir)) {
			throw new WriterException($this->realDir . ' path is not valid!');
		}
	}
	
	/**
	* @param array 	$item
	*/
	public function writeItem(array $item)
	{
		if (! array_key_exists('uid', $item)) {
			throw new WriterException('Typo3FileWriter: [uid] field does not exist');
		}
		
		if (! array_key_exists('pid', $item)) {
			throw new WriterException('Typo3FileWriter: [pid] field does not exist');
		}
		
		if (! array_key_exists('file', $item)) {
			throw new WriterException('Typo3FileWriter: [file] field does not exist');
		}
		
		// file data
		$fileName 	= $item['file'];
		$fileExt 	= end(explode('.', $fileName));
		$identifier = $this->fileDir . $fileName;
		
		// full path to file
		$file = $this->realDir . DIRECTORY_SEPARATOR . $fileName;
		
		if (! file_exists($file)) {
			return;
		}
		
		// file_sys item
		$fileItem = array(
			'storage' 			=> 1,
			'type' 				=> 2,
			'identifier' 		=> $identifier,
			'identifier_hash' 	=> sha1($identifier),
			'extension' 		=> $fileExt,
			'mime_type' 		=> 'image/' . $fileExt,
			'name' 				=> $fileName,
			'sha1' 				=> sha1_file($file),
			'size' 				=> filesize($file),
		);
		
		// insert file in sys_file
		$this->tableName = 'sys_file';
		parent::writeItem($fileItem);
		
		// insert is done, link to news
		if ($this->pdo->lastInsertid()) {
		
			// file_sys_reference item
			$refItem = array(
				'pid' 				=> $item['pid'],
				'sorting' 			=> 1,
				'uid_local' 		=> $this->pdo->lastInsertid(),
				'uid_foreign' 		=> $item['uid'],
				'tablenames' 		=> 'tx_news_domain_model_news',
				'fieldname' 		=> 'fal_media',
				'sorting_foreign' 	=> 1,
				'table_local' 		=> 'sys_file',
				'showinpreview' 	=> 1
			);
			
			// insert file reference in sys_file
			$this->tableName = 'sys_file_reference';
			parent::writeItem($refItem);
		}
	}
}