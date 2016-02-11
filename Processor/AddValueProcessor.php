<?php

namespace Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor;

class AddValueProcessor extends Processor
{
	public function exec($value, $n)
	{
		if (! is_numeric($n)) {
			throw new \Exception('AddValue: Value is not a number!');
		}
		
		return $value + $n;
	}
}