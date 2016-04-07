<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class AddProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class AddProcessor implements ProcessorInterface
{
    /**
     * @param float $value
     * @param float $amount
     * @return float
     */
    public static function exec($value, $amount = 0.0)
    {
        return $value + $amount;
    }
}