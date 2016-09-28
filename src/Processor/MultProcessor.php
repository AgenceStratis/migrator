<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class MultProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class MultProcessor extends Processor
{
    /**
     * @param float $value
     * @param float $amount
     * @return float
     */
    public static function exec($value, $amount = 1.0)
    {
        return $value * $amount;
    }
}