<?php

namespace Stratis\Component\Migrator\Processor\StringValue;
use Stratis\Component\Migrator\Processor;

class SplitProcessor extends StringProcessor
{
	public function exec($value, $delimiter)
	{
		parent::exec($value);
		
		if (! is_string($delimiter)) {
			throw new \Exception('Processor/String/Split: Delimiter is not a string !');
		}
		
		return explode($delimiter, $value);
	}
}