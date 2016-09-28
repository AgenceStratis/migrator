<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class StripTagsProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class StripTagsProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return strip_tags($text);
    }
}