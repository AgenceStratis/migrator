<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class StrToTimeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class StrToTimeProcessor implements ProcessorInterface
{
    /**
     * @param string $datetime
     * @param null $option
     * @return string
     */
    public static function exec($datetime, $option = null)
    {
        return strtotime($datetime);
    }
}