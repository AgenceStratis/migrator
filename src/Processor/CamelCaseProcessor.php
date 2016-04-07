<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class CamelCaseProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class CamelCaseProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param null $option
     * @return string
     */
    public static function exec($text, $option = null)
    {
        return ucwords(strtolower($text));
    }
}