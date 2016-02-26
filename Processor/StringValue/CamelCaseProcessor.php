<?php

namespace Stratis\Component\Migrator\Processor\StringValue;
use Stratis\Component\Migrator\Processor;

class CamelCaseProcessor extends Processor
{
	public function exec($value)
	{
		return ucwords($value);
	}
}