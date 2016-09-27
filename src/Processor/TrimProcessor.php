<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class TrimProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class TrimProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param null $option
     * @return string
     */
    public static function exec($text, $option = null)
    {
        return trim($text);
    }
}