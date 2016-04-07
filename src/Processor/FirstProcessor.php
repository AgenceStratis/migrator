<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class FirstProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class FirstProcessor implements ProcessorInterface
{
    /**
     * @param array $array
     * @param null $option
     * @return mixed
     */
    public static function exec($array, $option = null)
    {
        if(!empty($array)) {
            reset($array);
            return current($array);
        }
        return null;
    }
}