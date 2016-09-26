<?php

namespace Stratis\Component\Migrator\Processor;
use \Sunra\PhpSimple\HtmlDomParser;

/**
 * Class DocumentProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class DocumentProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param string $selector
     * @return array
     */
    public static function exec($text, $selector)
    {
        $document = HtmlDomParser::str_get_html($text);
        return array_map(function ($element) {
            return $element->innertext;
        }, $document->find($selector));
    }
}