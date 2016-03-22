<?php

namespace Stratis\Component\Migrator\Processor\ArrayValue;
use Stratis\Component\Migrator\Processor;

class FirstProcessor extends ArrayProcessor
{
	public function exec($value)
	{
		parent::exec($value);
		return empty($value) ? '' : $value[0];
	}
}