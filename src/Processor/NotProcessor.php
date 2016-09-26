<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class NotProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class NotProcessor implements ProcessorInterface
{
    /**
     * @param string $value
     * @param null $option
     * @return bool
     */
    public static function exec($value, $option = null)
    {
        return ! $value;
    }
}