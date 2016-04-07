<?php

namespace Stratis\Component\Migrator\Writer;

use Ddeboer\DataImport\Writer\CallbackWriter;

/**
 * Class CliWriter
 * @package Stratis\Component\Migrator\Writer
 */
class CliWriter extends CallbackWriter
{
    /**
     * CliWriter constructor.
     * Output item data in CLI
     */
    public function __construct()
    {
        parent::__construct(function ($item) {
            var_dump($item);
        });
    }
}
