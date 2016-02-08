<?php

namespace Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor;

/**
* Convert to an integer
*/
class IntegerProcessor extends Processor
{
	public function exec($value)
	{
		return intval($value);
	}
}
