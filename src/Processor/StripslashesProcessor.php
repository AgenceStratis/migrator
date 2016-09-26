<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class StripslashesProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class StripslashesProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param null $option
     * @return string
     */
    public static function exec($text, $option = null)
    {
        return stripslashes($text);
    }
}