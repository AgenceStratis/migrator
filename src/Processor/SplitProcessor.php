<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class SplitProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class SplitProcessor implements ProcessorInterface
{
    /**
     * @param array $array
     * @param $delimiter
     * @return string
     */
    public static function exec($array, $delimiter)
    {
        return explode($delimiter, $array);
    }
}