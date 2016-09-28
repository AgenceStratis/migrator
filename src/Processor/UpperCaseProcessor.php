<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class UpperCaseProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class UpperCaseProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return strtoupper($text);
    }
}