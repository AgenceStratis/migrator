<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class StripTagsProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class StripTagsProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param null $option
     * @return string
     */
    public static function exec($text, $option = null)
    {
        return strip_tags($text);
    }
}