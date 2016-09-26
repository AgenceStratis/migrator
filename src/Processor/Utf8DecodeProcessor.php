<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class Utf8DecodeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class Utf8DecodeProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param null $option
     * @return string
     */
    public static function exec($text, $option = null)
    {
        return utf8_decode($text);
    }
}