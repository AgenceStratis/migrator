<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class SetProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class SetProcessor implements ProcessorInterface
{
    /**
     * @param $value
     * @param $newValue
     * @return mixed
     */
    public static function exec($value, $newValue)
    {
        return $newValue;
    }
}