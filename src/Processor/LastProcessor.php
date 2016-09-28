<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class LastProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class LastProcessor extends Processor
{
    /**
     * @param array $array
     * @return mixed
     */
    public static function exec($array)
    {
        return empty($array) ? null : end($array);
    }
}