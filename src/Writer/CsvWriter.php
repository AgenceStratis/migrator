<?php

namespace Stratis\Component\Migrator\Writer;

use Stratis\Component\Migrator\Configuration;

/**
 * Class CsvWriter
 * @package Stratis\Component\Migrator\Writer
 *
 * @config string $filename
 * @config string $delimiter
 * @config string $enclosure
 * @config bool   $utf8
 */
class CsvWriter extends \Ddeboer\DataImport\Writer\CsvWriter
{
    /**
     * CsvWriter constructor.
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $filename   = $config->get(array('filename'));
        $delimiter  = $config->get(array('delimiter'), ',');
        $enclosure  = $config->get(array('enclosure'), '"');
        $utf8       = $config->get(array('utf8'), true);

        parent::__construct(
            $delimiter,
            $enclosure,
            fopen($filename, 'w'),
            $utf8
        );
    }
}
