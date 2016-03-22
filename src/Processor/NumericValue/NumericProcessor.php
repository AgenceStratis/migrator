<?php

namespace Stratis\Component\Migrator\Processor\NumericValue;
use Stratis\Component\Migrator\Processor;

abstract class NumericProcessor extends Processor
{
	public function exec($value, $amount)
	{
		if (! is_numeric($value) && ! is_null($value)) {
			throw new \Exception('Processor/Numeric: Value is not a number !');
		}
		
		if (! is_numeric($amount)) {
			throw new \Exception('Processor/Numeric: Amount is not a number !');
		}
	}
}