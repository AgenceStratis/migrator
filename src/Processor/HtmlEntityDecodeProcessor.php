<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class HtmlEntityDecodeProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class HtmlEntityDecodeProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param null $option
     * @return string
     */
    public static function exec($text, $option = null)
    {
        return html_entity_decode($text);
    }
}