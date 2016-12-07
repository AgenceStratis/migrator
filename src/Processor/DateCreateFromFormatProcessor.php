<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class DateCreateFromFormatProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class DateCreateFromFormatProcessor extends Processor
{
    /**
     * @param string $date
     * @param string $format
     * @return \DateTime
     */
    public static function exec($date, $format)
    {
        return date_create_from_format($format, $date);
    }
}