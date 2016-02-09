<?php

namespace Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor;

class ConvertProcessor extends Processor
{
	public function exec($value, $type)
	{
		$newValue = $value;
		
		switch (strtolower($type)) {
			
			case 'str':
			case 'string':
				$newValue = strval($value);
				break;
			
			case 'int':
			case 'integer':
				$newValue = intval($value);
				break;
		}
		
		return $newValue;
	}
}
