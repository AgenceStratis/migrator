<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class UpperCaseProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class UpperCaseProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param null $option
     * @return string
     */
    public static function exec($text, $option = null)
    {
        return strtoupper($text);
    }
}