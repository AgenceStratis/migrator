<?php

namespace Stratis\Component\Migrator\Processor\NumericValue;
use Stratis\Component\Migrator\Processor;

class ModProcessor extends NumericProcessor
{
	public function exec($value, $max)
	{
		parent::exec($value, $max);
		return $value % $max;
	}
}