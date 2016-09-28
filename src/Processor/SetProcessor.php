<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class SetProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class SetProcessor extends Processor
{
    /**
     * @param mixed $value
     * @param mixed $newValue
     * @return mixed
     */
    public static function exec($value, $newValue)
    {
        return $newValue;
    }
}