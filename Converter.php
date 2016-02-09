<?php

namespace Stratis\Component\Migrator;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

use Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor\IntegerProcessor;
use Stratis\Component\Migrator\Processor\UpperCaseProcessor;

/**
* ItemConverter for Migrator
*/
class Converter implements ItemConverterInterface
{
	protected $configuration = array();
	protected $processors = array();
	
	/**
	* Constructor
	*
	* @param array $configuration
	*/
	public function __construct($configuration)
	{
		$this->configuration = $configuration;
		
		$this->processors = array(
			'toInteger' => new IntegerProcessor(ON_VALUES),
			'upperCase' => new UpperCaseProcessor(ON_VALUES | ON_FIELDS)
		);
	}
	
	/**
	* Set up a new processor
	* Can override basic functions such as "upperCase"
	*
	* @param string $key
	* @param object $processor
	*/
	public function set(string $key, object $processor)
	{
		$this->processors[$key] = $processor;
	}
	
	/**
	* Route
	* Get basic fields for future redirection
	*
	* @param array $item 
	*/
	protected function route($item)
	{
		$route = array();
		
		foreach ($item as $key => $value) {
			$route[$key] = $key;
		}
		
		return $route;
	}
	
	/**
	* Process route
	* Change fields name according to $route
	*
	* @param array $item
	* @param array $route
	*/
	protected function processRoute($item, $route)
	{
		$routedItem = array();
		
		foreach ($route as $baseField => $targetField) {
			$routedItem[$targetField] = $item[$baseField];
		}
		
		return $routedItem;
	}
	
	/**
	* Convert
	* Apply custom rules and processors to an item
	*
	* @param array $item
	*/
	public function convert($item)
	{
		$route = $this->route($item);
		
		// Process values
		foreach ($this->configuration['values'] as $field => $params) {
			
			// An array of key has been given
			if (is_array($params) && count($params) > 0) {
				
				$value = $item[$field];
				
				// Search for matching processors
				foreach ($params as $k) {
					if (array_key_exists($k, $this->processors)) {
						$value = $this->processors[$k]->exec($value);
					}
				}
				
				$item[$field] = $value;
			}
			
			// Assign data to this value
			if (! is_array($params) && ! is_object($params)) {
				$item[$field] = $params;
			}
		}
		
		// Process field names
		foreach ($this->configuration['fields'] as $field => $params) {
			
			// An array of key has been given
			if (is_array($params) && count($params) > 0) {
				
				$newKey = $field;
				
				// Search for matching processors
				foreach ($params as $k) {
					if (array_key_exists($k, $this->processors)) {
						$newKey = $this->processors[$k]->exec($newKey);
					}
				}
				
				// Replace old key by the new one
				$item[$newKey] = $item[$field];
				unset($item[$field]);
			}
			
			// Assign new name to this field
			if (is_string($params) && array_key_exists($params, $item)) {
				$route[$field] = $params;
			}
		}
		
		// Apply routes (fields changenames)
		return $this->processRoute($item, $route);
	}
}