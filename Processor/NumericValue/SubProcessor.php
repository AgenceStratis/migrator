<?php

namespace Stratis\Component\Migrator\Processor\NumericValue;
use Stratis\Component\Migrator\Processor;

class SubProcessor extends NumericProcessor
{
	public function exec($value, $amount)
	{
		parent::exec($value, $amount);
		return $value - $amount;
	}
}