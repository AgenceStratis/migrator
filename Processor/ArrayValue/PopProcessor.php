<?php

namespace Stratis\Component\Migrator\Processor\ArrayValue;
use Stratis\Component\Migrator\Processor;

class PopProcessor extends ArrayProcessor
{
	public function exec($values, $repeat)
	{
		parent::exec($values);
		
		if (! is_int($repeat)) {
			throw new \Exception('Processor/Array/Pop: Repeat is not an integer !');
		}
		
		for ($i = 0; $i < $repeat; $i++) {
			array_pop($values);
		}
		
		return $values;
	}
}