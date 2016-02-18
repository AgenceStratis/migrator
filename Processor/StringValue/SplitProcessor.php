<?php

namespace Stratis\Component\Migrator\Processor\StringValue;
use Stratis\Component\Migrator\Processor;

class SplitProcessor extends Processor
{
	public function exec($value, $delimiter)
	{
		if (! is_string($value)) {
			throw new \Exception('Processor/String/Split: Value is not a string !');
		}
		
		if (! is_string($delimiter)) {
			throw new \Exception('Processor/String/Split: Delimiter is not a string !');
		}
		
		return explode($delimiter, $value);
	}
}