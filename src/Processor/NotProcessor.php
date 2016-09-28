<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class NotProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class NotProcessor extends Processor
{
    /**
     * @param mixed $value
     * @return bool
     */
    public static function exec($value)
    {
        return ! $value;
    }
}