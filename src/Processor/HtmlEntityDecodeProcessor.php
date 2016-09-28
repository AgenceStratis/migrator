<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class HtmlEntityDecodeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class HtmlEntityDecodeProcessor extends Processor
{
    /**
     * @param string $text
     * @return string
     */
    public static function exec($text)
    {
        return html_entity_decode($text);
    }
}