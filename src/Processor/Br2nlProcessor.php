<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class Br2nlProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class Br2nlProcessor extends Processor
{
    /**
     * @param mixed $text
     * @return bool
     */
    public static function exec($text)
    {
        return preg_replace('#<br\s*/?>#i', "\n", $text);
    }
}