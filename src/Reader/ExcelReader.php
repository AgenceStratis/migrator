<?php

namespace Stratis\Component\Migrator\Reader;

use Stratis\Component\Migrator\Configuration;

/**
 * Class ExcelReader
 * @package Stratis\Component\Migrator\Reader
 *
 * @config string $filename
 * @config bool   $header
 * @config int    $sheet
 */
class ExcelReader extends \Ddeboer\DataImport\Reader\ExcelReader
{
    /**
     * ExcelReader constructor.
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $filename   = $config->get(array('filename'));
        $header     = $config->get(array('header'), false) ? 0 : null;
        $sheet      = $config->get(array('sheet'), 0);

        parent::__construct(new \SplFileObject($filename), $header, $sheet);
    }
}
