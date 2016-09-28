<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class CamelCaseProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class CamelCaseProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return ucwords(strtolower($text));
    }
}