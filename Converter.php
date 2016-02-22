<?php

namespace Stratis\Component\Migrator;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

use Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor\ArrayValue;
use Stratis\Component\Migrator\Processor\NumericValue;
use Stratis\Component\Migrator\Processor\StringValue;

/**
* ItemConverter for Migrator
*/
class Converter implements ItemConverterInterface
{	
	/**
	* @var array
	*/
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
			'copy' 		=> new Processor(ON_VALUES),
			
			// Mixed values processors
			'set' 		=> new Processor\SetProcessor(ON_VALUES | ON_FIELDS),
			'convert' 	=> new Processor\ConvertProcessor(ON_VALUES),
			
			// String processors
			'split' 				=> new StringValue\SplitProcessor(ON_VALUES),
			'upper_case' 			=> new StringValue\UpperCaseProcessor(ON_VALUES | ON_FIELDS),
			'replace' 				=> new StringValue\ReplaceProcessor(ON_VALUES | ON_FIELDS),
			'html_entity_decode' 	=> new StringValue\HtmlEntityDecodeProcessor(ON_VALUES),
			'strip_tags' 			=> new StringValue\StripTagsProcessor(ON_VALUES),
			
			// Numeric processors
			'add' 	=> new NumericValue\AddProcessor(ON_VALUES),
			'sub' 	=> new NumericValue\SubProcessor(ON_VALUES),
			'mult' 	=> new NumericValue\MultProcessor(ON_VALUES),
			'div' 	=> new NumericValue\DivProcessor(ON_VALUES),
			
			// Array processors
			'first' => new ArrayValue\FirstProcessor(ON_VALUES),
			'join' 	=> new ArrayValue\JoinProcessor(ON_VALUES),
			'last' 	=> new ArrayValue\LastProcessor(ON_VALUES),
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
				
				// if it doesn't exist, create new null column
				if (! array_key_exists($field, $item)) {
					$item[$field] = null;
					$route[$field] = $field;
				}
				
				$newValue = $item[$field];
				
				// copy value from another item field
				if (array_key_exists('copy', $params)) {
					$copyFrom = $params['copy'];
					if (array_key_exists($copyFrom, $item)) {
						$item[$field] = $item[$copyFrom];
					}
					continue;
				}
				
				// Search for matching processors
				foreach ($params as $paramKey => $paramValue) {
					if (array_key_exists($paramKey, $this->processors)) {
						$newValue = $this->processors[$paramKey]->exec($newValue, $paramValue);
					}
				}
				
				$item[$field] = $newValue;
			}
			
			// Assign data to this value
			if ((is_string($params) && strlen($params) > 0) || is_numeric($params) || is_null($params)) {
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
					}
					continue;
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
			if (is_string($params) && strlen($params) > 0) {
				$route[$params] = $field;
				unset($route[$field]);
			}
		}
		
		// Apply routes (fields changenames)
		return $this->processRoute($item, $route);
	}
}