<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class StrToTimeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class StrToTimeProcessor extends Processor
{
    /**
     * @param string $datetime
     * @return string
     */
    public static function exec($datetime)
    {
        return strtotime($datetime);
    }
}