<?php

namespace Stratis\Component\Migrator\Processor\StringValue;
use Stratis\Component\Migrator\Processor;

abstract class StringProcessor extends Processor
{
	public function exec($value)
	{
		if (! is_string($value) && ! strlen($value)) {
			throw new \Exception('Processor/String: Value is not a string !');
		}
	}
}