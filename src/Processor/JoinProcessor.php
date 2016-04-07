<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class JoinProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class JoinProcessor implements ProcessorInterface
{
    /**
     * @param array $array
     * @param $delimiter
     * @return string
     */
    public static function exec($array, $delimiter)
    {
        return implode($delimiter, $array);
    }
}