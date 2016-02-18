<?php

namespace Stratis\Component\Migrator\Processor\ArrayValue;
use Stratis\Component\Migrator\Processor;

abstract class ArrayProcessor extends Processor
{
	public function exec($value)
	{
		if (! is_array($value)) {
			throw new \Exception('Processor/Array: Value is not an array !');
		}
	}
}