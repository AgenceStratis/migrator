<?php

namespace Stratis\Component\Migrator\Processor\StringValue;
use Stratis\Component\Migrator\Processor;

class UpperCaseProcessor extends Processor
{
	public function exec($value)
	{
		return strtoupper($value);
	}
}