<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Interface ProcessorInterface
 * @package Stratis\Component\Migrator
 */
interface ProcessorInterface
{
    /**
     * @param $value
     * @param $option
     * @return mixed
     */
    public static function exec($value, $option);
}
