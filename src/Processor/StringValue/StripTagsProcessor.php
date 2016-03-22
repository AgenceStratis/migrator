<?php

namespace Stratis\Component\Migrator\Processor\StringValue;
use Stratis\Component\Migrator\Processor;

class StripTagsProcessor extends StringProcessor
{
	public function exec($value)
	{
		parent::exec($value);
		
		return strip_tags($value);
	}
}