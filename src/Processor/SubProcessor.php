<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class SubProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class SubProcessor extends Processor
{
    /**
     * @param float $value
     * @param float $amount
     * @return float
     */
    public static function exec($value, $amount = 0.0)
    {
        return $value - $amount;
    }
}