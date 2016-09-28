<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class TrimProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class TrimProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return trim($text);
    }
}