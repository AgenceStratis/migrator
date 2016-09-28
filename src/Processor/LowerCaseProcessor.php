<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class LowerCaseProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class LowerCaseProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return strtolower($text);
    }
}