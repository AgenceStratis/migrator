<?php

namespace Stratis\Component\Migrator\Processor;

/**
 * Class ConvertProcessor
 * @package Stratis\Component\Migrator\Processor
 */
class ConvertProcessor implements ProcessorInterface
{
    /**
     * @param $value
     * @param string $type
     * @return mixed
     */
    public static function exec($value, $type)
    {
        switch (strtolower($type)) {
            case 'str':
            case 'string': {
                return strval($value);
            }
            case 'int':
            case 'integer': {
                return intval($value);
            }
            default: {
                return $value;
            }
        }
    }
}
