<?php

namespace Stratis\Component\Migrator\Reader;

use Stratis\Component\Migrator\Configuration;

/**
 * Class CsvReader
 * @package Stratis\Component\Migrator\Reader
 *
 * @config string $filename
 * @config string $delimiter
 * @config bool   $header
 */
class CsvReader extends \Ddeboer\DataImport\Reader\CsvReader
{
    /**
     * CsvReader constructor.
     * @param Configuration $config
     * @throws \Exception
     */
    public function __construct(Configuration $config)
    {
        $filename   = $config->get(array('filename'));
        $delimiter  = $config->get(array('delimiter'), ',');
        $header     = $config->get(array('header'), true);

        parent::__construct(
            new \SplFileObject($filename),
            $delimiter
        );

        if ($header) {
            $this->setHeaderRowNumber(0);
        }
    }
}
