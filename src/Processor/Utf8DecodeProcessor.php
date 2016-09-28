<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class Utf8DecodeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class Utf8DecodeProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return utf8_decode($text);
    }
}