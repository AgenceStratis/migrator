<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class TimeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class TimeProcessor extends Processor
{
    /**
     * @return int
     */
    public static function exec()
    {
        return time();
    }
}