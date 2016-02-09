<?php

namespace Stratis\Component\Migrator;

define('ON_VALUES', 1);
define('ON_FIELDS', 2);

class Processor
{
	/*
	* @var boolean
	*/
	protected $onValues = false;
	protected $onFields = false;
	
	/**
	* Pass a bitmask to the constructor and set processor parameters
	* Tell if the processor can be applied ON_VALUES and/or ON_FIELDS
	*
	* examples:
	* 	new Processor(ON_VALUES)
	* 	new Processor(ON_FIELDS)
	* 	new Processor(ON_VALUES | ON_FIELDS)
	*
	* @param $options
	*/
	public function __construct($options = 0)
	{
		if ($options & ON_VALUES) {
			$this->onValues = true;
		}
		
		if ($options & ON_FIELDS) {
			$this->onFields = true;
		}
	}
	
	/**
	* @param $value
	* @param $param
	*
	* @return $value
	*/
	public function exec($value, $param = null)
	{
		return $value;
	}
}
