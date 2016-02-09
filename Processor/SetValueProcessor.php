<?php

namespace Stratis\Component\Migrator\Processor;
use Stratis\Component\Migrator\Processor;

/**
* Set value to an item
*/
class SetValueProcessor extends Processor
{
	public function exec($oldValue, $newValue)
	{
		return $newValue;
	}
}