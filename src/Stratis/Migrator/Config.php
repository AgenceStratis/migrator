<?php

namespace Stratis\Migrator;

/**
 * Config parser for Migrator
 */
class Config
{
	protected $confData = array();
	
	/**
	 * Constructor.
	 *
	 * @param array $fileConf Parsed YAML file
	 */
	public function __construct($fileConf)
	{
		$options = array('file', 'fields' => array(), 'database_type',
			'database_name', 'server', 'username', 'password', 'charset', 'table');
		
		$baseConf = array(
			'source' => array('type', 'options' => $options),
			'dest' => array('type', 'options' => $options),
			'processors' => array('values' => array(), 'fields' => array()));
		
		$this->confData = array_merge($baseConf, $fileConf);
	}
	
	/**
	 * Get config value(s)
	 *
	 * Crawl confData recursively, using arguments as array keys.
	 * Return null if item doesn't exists.
	 *
	 * Usage: $config->get('source', 'options', 'file');
	 *
	 * @param string Each key must be a string
	 *
	 * @return mixed Return found data
	 */
	public function get()
	{
		if (func_num_args() == 0) {
			return;
		}
		
		$search = $this->confData;
		
		foreach (func_get_args() as $arg) {
			if (array_key_exists($arg, $search)) {
				$search = $search[ $arg ];
			} else {
				$search = null;
				break;
			}
		}
		
		return $search;
	}
}