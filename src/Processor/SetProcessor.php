<?php

namespace Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor;

class SetProcessor extends Processor
{
	public function exec($oldValue, $newValue)
	{
		return $newValue;
	}
}