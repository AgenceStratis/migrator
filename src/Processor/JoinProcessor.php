<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class JoinProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class JoinProcessor extends Processor
{
    /**
     * @param array $array
     * @param string $delimiter
     * @return string
     */
    public static function exec($array, $delimiter)
    {
        return implode($delimiter, $array);
    }
}