<?php

namespace Stratis\Component\Migrator\Processor\StringValue;
use Stratis\Component\Migrator\Processor;

class HtmlEntityDecodeProcessor extends StringProcessor
{
	public function exec($value)
	{
		parent::exec($value);
		
		return html_entity_decode($value);
	}
}