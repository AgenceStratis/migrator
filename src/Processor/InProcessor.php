<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class InProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class InProcessor implements ProcessorInterface
{
    /**
     * @param string $value
     * @param string $stack
     * @return bool
     */
    public static function exec($value, $stack)
    {
        $stack = array_map('trim', explode(',', $stack));
        return in_array($value, $stack);
    }
}