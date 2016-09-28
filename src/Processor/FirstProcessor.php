<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class FirstProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class FirstProcessor extends Processor
{
    /**
     * @param array $array
     * @return mixed
     */
    public static function exec($array)
    {
        if(!empty($array)) {
            reset($array);
            return current($array);
        }
        return null;
    }
}