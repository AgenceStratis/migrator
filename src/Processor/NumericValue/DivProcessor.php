<?php

namespace Stratis\Component\Migrator\Processor\NumericValue;
use Stratis\Component\Migrator\Processor;

class DivProcessor extends NumericProcessor
{
	public function exec($value, $amount)
	{
		parent::exec($value, $amount);
		
		if ($amount === 0) {
			throw new \Exception('Processor/Numeric/Div: Cannot divide by 0 !');
		}
		
		return $value / $amount;
	}
}