<?php

namespace Stratis\Component\Migrator\Reader;

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
     * @param $options
     */
    public function __construct($options)
    {
        $filename   = $options['filename'];
        $header     = $options['header'];
        $sheet      = $options['sheet'];

        parent::__construct(
            new \SplFileObject($filename),
            ($header ? 0 : null),
            $sheet
        );
    }
}
