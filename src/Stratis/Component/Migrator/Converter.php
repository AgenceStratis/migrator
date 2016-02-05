<?php

//TODO: remove references usage

namespace Stratis\Component\Migrator;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

/**
* ItemConverter for Migrator
*/
class Converter implements ItemConverterInterface {
	
	protected $processors = array();
	
	// rulesValues = array('round', 'toInteger')...
	// rulesFields = array('upperCase', 'lowerCase')...
		
	/**
	* Constructor
	*
	* @param array $processors Parameters from Migrator Config
	*/
	public function __construct ($processors)
	{
		$this->processors = $processors;
	}
	
	/**
	* Process values
	* Apply value modification according to processors (rules)
	*
	* @param array $item 
	*/
	protected function processValues(&$item)
	{
		foreach ($this->processors['values'] as $field => $params) {
			
			// params = array (multiples functions)
			if (is_array($params) && count($params > 0)) {
				
				$value = $item[$field];
			
				if (in_array('upperCase', $params)) {
					$value = strtoupper($value);
				}
			
				if (in_array('stripTags', $params)) {
					$value = strip_tags($value);
				}
			
				if (in_array('round', $params)) {
					$value = $value | 0;
				}
				
				$item[$field] = $value;
			}
			
			// params = string (assign data)
			if (is_string($params)) {
				
				$item[$field] = $params;
			}
		}
	}
	
	/**
	* route
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
	* Process fields
	* Apply fields modifications according to processors (rules)
	*
	* @param array $item
	*/
	protected function processFields (&$item, &$route)
	{
		foreach ($this->processors['fields'] as $field => $params) {
			
			// params = array (multiples functions)
			if (is_array($params) && count($params) > 0) {
				
				$newKey = $field;
				
				if (in_array('lowerCase', $params)) {
					$newKey = strtolower($newKey);
				}
				
				if (in_array('upperCase', $params)) {
					$newKey = strtoupper($newKey);
				}
			
				if (in_array('stripTags', $params)) {
					$newKey = strip_tags($newKey);
				}
				
				$item[$newKey] = $item[$field];
				unset($item[$field]);
			}
			
			// params = string (assign data)
			if (is_string($params) && array_key_exists($params, $item)) {
				$route[$field] = $params;
			}
		}
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
		
		$this->processValues($item);
		$this->processFields($item, $route);
		
		return $this->processRoute($item, $route);
	}
}