<?php

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class Converter implements ItemConverterInterface {
	
	protected $processors = array();
	
	// rulesValues = array( 'round', 'toInteger' )...
	// rulesFields = array( 'upperCase', 'lowerCase' )...
	
	public function __construct ( $processors ) {
		
		$this->processors = $processors;
	}
	
	protected function processValues ( &$item ) {
		
		if ( ! array_key_exists( 'values', $this->processors )) {
			return;
		}
		
		foreach ( $this->processors['values'] as $field => $params ) {
			
			// params = array (multiples functions)
			if ( is_array( $params ) && count( $params > 0 )) {
				
				$value = $item[ $field ];
			
				if ( in_array( 'upperCase', $params )) {
					$value = strtoupper( $value );
				}
			
				if ( in_array( 'stripTags', $params )) {
					$value = strip_tags( $value );
				}
			
				if ( in_array( 'round', $params )) {
					$value = $value | 0;
				}
				
				$item[ $field ] = $value;
			}
			
			// params = string (assign data)
			if ( is_string( $params )) {
				
				$item[ $field ] = $params;
			}
		}
	}
	
	protected function route ( $item ) {
		
		$route = array();
		
		foreach ( $item as $key => $v ) {
			
			$route[ $key ] = $key;
		}
		
		return $route;
	}
	
	protected function processFields ( &$item, &$route ) {
		
		if ( ! array_key_exists( 'fields', $this->processors )) {
			return;
		}
		
		foreach ( $this->processors['fields'] as $field => $params ) {
			
			// params = array (multiples functions)
			if ( is_array( $params ) && count( $params > 0 )) {
				
				$newKey = $field;
				
				if ( in_array( 'lowerCase', $params )) {
					$newKey = strtolower( $newKey );
				}
				
				if ( in_array( 'upperCase', $params )) {
					$newKey = strtoupper( $newKey );
				}
			
				if ( in_array( 'stripTags', $params )) {
					$newKey = strip_tags( $newKey );
				}
				
				$item[ $newKey ] = $item[ $field ];
				unset( $item[ $field ]);
			}
			
			// params = string (assign data)
			if ( is_string( $params )) {
				
				if ( array_key_exists( $params, $item )) {
					
					$route[ $field ] = $params;
				}
			}
		}
	}
	
	protected function processRoute ( $item, $route ) {
		
		$routedItem = array();
		
		foreach ( $route as $baseField => $targetField ) {
			
			$routedItem[ $targetField ] = $item[ $baseField ];
		}
		
		return $routedItem;
	}
	
	public function convert( $item ) {
		
		$route = $this->route( $item );
		
		$this->processValues( $item );
		$this->processFields( $item, $route );
		
		return $this->processRoute( $item, $route );
	}
}