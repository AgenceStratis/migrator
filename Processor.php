<?php

namespace Stratis\Component\Migrator;

define('ON_VALUES', 1);
define('ON_FIELDS', 2);

class Processor
{
	protected $onValues = false;
	protected $onFields = false;
	
	public function __construct($options)
	{
		if ($options & ON_VALUES) {
			$this->onValues = true;
		}
		
		if ($options & ON_FIELDS) {
			$this->onFields = true;
		}
	}
	
	public function exec($value)
	{
		return $value;
	}
}
