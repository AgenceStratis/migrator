<?php

namespace Stratis\Component\Migrator\Reader;
use Stratis\Component\Migrator\Configuration;
use Ddeboer\DataImport\Reader\ReaderInterface;

/**
 * Class JsonReader
 * @package Stratis\Component\Migrator\Reader
 */
class JsonReader extends ReaderInterface
{
    /**
     * JsonReader constructor.
     * @param Configuration $config
     * @throws \Exception
     */
	public function __construct(Configuration $config)
	{
        $filename = $config->get(array('filename'));

        if ($filename == null) {
            throw new \Exception('Filename is not defined');
        }

		$this->data = json_decode(
			file_get_contents($filename)
		);
	}

    /**
     * @return array
     */
    public function getFields()
    {
        return array_keys(
            current($this->data)
        );
    }
}
