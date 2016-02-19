<?php

namespace Stratis\Component\Migrator\Processor\StringValue;
use Stratis\Component\Migrator\Processor;

class ReplaceProcessor extends Processor
{
	public function exec($value, $args)
	{
		if (! is_array($args) || count($args) !== 2
		|| ! is_string($args[0]) || ! is_string($args[1])) {
			
			throw new \Exception('Processor/String/Replace: Argument must be an array of 2 strings !');
		}
		
		return str_replace($args[0], $args[1], $value);
	}
}