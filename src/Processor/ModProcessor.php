<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class ModProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class ModProcessor extends Processor
{
    /**
     * @param float $value
     * @param float $amount
     * @return float
     */
    public static function exec($value, $amount)
    {
        return $value % $amount;
    }
}