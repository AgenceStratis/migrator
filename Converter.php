<?php

namespace Stratis\Component\Migrator;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

use Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor\ConvertProcessor;
use Stratis\Component\Migrator\Processor\UpperCaseProcessor;
use Stratis\Component\Migrator\Processor\SetValueProcessor;

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
			'delete' 	=> new Processor(ON_FIELDS),
			'set' 		=> new SetValueProcessor(ON_VALUES | ON_FIELDS),
			'convert' 	=> new ConvertProcessor(ON_VALUES),
			'upperCase' => new UpperCaseProcessor(ON_VALUES | ON_FIELDS)
		);
	}
	
	/**
	* Set up a (new) processor
	* Can override basic functions such as "upperCase"
	*
	* @param string $key
	* @param object $processor
	*/
	public function setProcessor($key, $processor)
	{
		$this->processors[$key] = $processor;
	}
	
	/**
	* Route
	* Get basic fields for future redirection
	* $route[futureKey] = actualKey
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
			if (array_key_exists($targetField, $item)) {
				$routedItem[$baseField] = $item[$targetField];
			}
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
				
				$newValue = $item[$field];
				
				// Search for matching processors
				foreach ($params as $paramKey => $paramValue) {
					if (array_key_exists($paramKey, $this->processors)) {
						$newValue = $this->processors[$paramKey]->exec($newValue, $paramValue);
					}
				}
				
				$item[$field] = $newValue;
			}
			
			// Assign data to this value
			if ((is_string($params) && strlen($params) > 0) || is_numeric($params)) {
				$item[$field] = $params;
			}
		}
		
		// Process field names
		foreach ($this->configuration['fields'] as $field => $params) {
			
			// An array of key has been given
			if (is_array($params) && count($params) > 0) {
				
				$newField = $field;
				
				// remove column
				if (array_key_exists('delete', $params)) {
					if ($params['delete']) {
						unset($route[$field]);
						continue;
					}
				}
				
				// Search for matching processors
				foreach ($params as $paramKey => $paramValue) {
					if (array_key_exists($paramKey, $this->processors)) {
						$newField = $this->processors[$paramKey]->exec($newField, $paramValue);
					}
				}
				
				// Replace old key by the new one
				if ($newField != $field) {
					$route[$newField] = $field;
					unset($route[$field]);
				}
			}
			
			// Assign new name to this field
			if ((is_string($params) && strlen($params) > 0) || is_numeric($params)) {
				$route[$field] = $params;
			}
		}
		
		// Apply routes (fields changenames)
		return $this->processRoute($item, $route);
	}
}