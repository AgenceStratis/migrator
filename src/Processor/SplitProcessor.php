<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class SplitProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class SplitProcessor extends Processor
{
    /**
     * @param array $array
     * @param string $delimiter
     * @return string
     */
    public static function exec($array, $delimiter)
    {
        return explode($delimiter, $array);
    }
}