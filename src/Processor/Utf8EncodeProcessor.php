<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class Utf8EncodeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class Utf8EncodeProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return utf8_encode($text);
    }
}