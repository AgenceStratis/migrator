<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class DateTimeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class DateTimeProcessor extends Processor
{
    /**
     * @param string $date
     * @param string $timezone
     * @return \DateTime
     */
    public static function exec($date, $timezone)
    {
        return new \DateTime($date, $timezone);
    }
}