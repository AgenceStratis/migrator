<?php

namespace Stratis\Component\Migrator\Reader;
use Ddeboer\DataImport\Reader;

class JsonReader implements Reader
{
	public function __construct(array $data)
	{
		$this->data = json_decode($data);
	}
}