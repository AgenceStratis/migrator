<?php

namespace Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor;

/**
* Set a string on uppercase
*/
class UpperCaseProcessor extends Processor
{
	public function exec($value)
	{
		return strtoupper($value);
	}
}