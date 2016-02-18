<?php

namespace Stratis\Component\Migrator\Processor\ArrayValue;
use Stratis\Component\Migrator\Processor;

class LastProcessor extends ArrayProcessor
{
	public function exec($value)
	{
		parent::exec($value);
		return empty($value) ? '' : end($value);
	}
}