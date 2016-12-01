<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class Nl2brProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class Nl2brProcessor extends Processor
{
    /**
     * @param mixed $text
     * @return bool
     */
    public static function exec($text)
    {
        return nl2br($text);
    }
}