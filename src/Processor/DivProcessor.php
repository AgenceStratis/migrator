<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class DivProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class DivProcessor implements ProcessorInterface
{
    /**
     * @param float $value
     * @param float $amount
     * @return float
     * @throws \Exception
     */
    public static function exec($value, $amount = 1.0)
    {
        if ($amount == 0) {
            throw new \Exception('Processor/Numeric/Div: Cannot divide by 0 !');
        }

        return $value / $amount;
    }
}