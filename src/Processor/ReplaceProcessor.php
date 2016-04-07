<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class ReplaceProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class ReplaceProcessor implements ProcessorInterface
{
    /**
     * @param string $text
     * @param array $args
     * @return string
     * @throws \Exception
     */
    public static function exec($text, $args = array())
    {
        if (count($args) !== 2) {
            throw new \Exception('Argument must be an array of 2 strings !');
        }

        return str_replace($args[0], $args[1], $text);
    }
}