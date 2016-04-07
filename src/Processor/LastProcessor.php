<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class LastProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class LastProcessor implements ProcessorInterface
{
    /**
     * @param array $array
     * @param null $option
     * @return mixed
     */
    public static function exec($array, $option = null)
    {
        return empty($array) ? null : end($array);
    }
}