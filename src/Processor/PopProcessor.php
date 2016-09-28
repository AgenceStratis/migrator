<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class PopProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class PopProcessor extends Processor
{
    /**
     * @param array $array
     * @param int $repeat
     * @return array
     */
    public static function exec($array, $repeat = 1)
    {
        for ($i = 0; $i < $repeat; $i++) {
            array_pop($array);
        }

        return $array;
    }
}