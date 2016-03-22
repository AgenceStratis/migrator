<?php

namespace Stratis\Component\Migrator\Processor\ArrayValue;
use Stratis\Component\Migrator\Processor;

class JoinProcessor extends ArrayProcessor
{
	public function exec($value, $delimiter)
	{
		parent::exec($value);
		
		if (! is_string($delimiter)) {
			throw new \Exception('Processor/Array/Join: Delimiter is not a string !');
		}
		
		return implode($delimiter, $value);
	}
}