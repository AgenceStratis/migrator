<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class LowerCaseProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class LowerCaseProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param null $option
     * @return string
     */
    public static function exec($text, $option = null)
    {
        return strtolower($text);
    }
}