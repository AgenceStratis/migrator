<?php

namespace Stratis\Component\Migrator\Writer;
use Ddeboer\DataImport\Writer\AbstractStreamWriter;

class JsonWriter extends AbstractStreamWriter
{
	protected $count = 0;
	
	public function prepare()
	{
		fwrite($this->getStream(), "[");
	}
	
	public function writeItem(array $item)
	{
		if ($this->count++ > 0) {
			fwrite($this->getStream(), ",\n");
		}
		
		fwrite($this->getStream(), json_encode($item, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
	}
	
	public function finish()
	{
		fwrite($this->getStream(), "]\n");
	}
}