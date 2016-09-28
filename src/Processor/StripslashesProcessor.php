<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class StripslashesProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class StripslashesProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return stripslashes($text);
    }
}