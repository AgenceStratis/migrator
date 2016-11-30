<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class DateFormatProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class DateFormatProcessor extends Processor
{
    /**
     * @param \DateTime $datetime
     * @param string $format
     * @return string
     */
    public static function exec($datetime, $format)
    {
        return date_format($datetime, $format);
    }
}