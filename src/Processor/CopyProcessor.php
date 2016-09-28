<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class CopyProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class CopyProcessor extends Processor
{
    /**
     * @param float $value
     * @param mixed $column
     * @param array $record
     * @return float
     * @throws \Exception
     */
    public static function exec($value, $column, $record)
    {
        if (!array_key_exists($column, $record)) {
            throw new \Exception(sprintf("Column '%s' does not exist in this table", $column));
        }
        return $record[$column];
    }
}