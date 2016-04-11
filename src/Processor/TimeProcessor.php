<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class TimeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class TimeProcessor implements ProcessorInterface
{
    /**
     * @param null $text
     * @param null $option
     * @return int
     */
    public static function exec($text = null, $option = null)
    {
        return time();
    }
}