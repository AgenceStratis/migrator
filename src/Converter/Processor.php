<?php

namespace Stratis\Component\Migrator\Converter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

/**
 * Class Processor
 * @package Stratis\Component\Migrator\Converter
 */
class Processor implements ItemConverterInterface
{
    /**
     * @var array
     */
    private $processors = array();

    /**
     * Processor constructor.
     * @param array $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * @param mixed $item
     * @return mixed
     * @throws \Exception
     */
    public function convert($item)
    {
        foreach ($this->processors as $field => $actions) {

            // If field doesn't exist:
            // - Create new column with null value
            // - Add new route to this column
            if (!array_key_exists($field, $item)) {
                $item[$field] = null;
                $this->route[$field] = $field;
            }

            // An array of actions has been given
            if (is_array($actions) && count($actions) > 0) {

                foreach ($actions as $action) {

                    // Get action name and options
                    $name = is_array($action) ? key($action) : $action;
                    $options = is_array($action) ? current($action) : null;

                    // Set processor name in CamelCase
                    $name = implode('', array_map('ucwords', explode('_', $name)));

                    // Get processor class from action name
                    $class = 'Stratis\Component\Migrator\Processor\\' . $name . 'Processor';

                    // Check if class exists
                    if (!class_exists($class)) {
                        throw new \Exception($class . ' does not exists');
                    }

                    // Use processor exec function
                    $item[$field] = $class::exec($item[$field], $options);
                }
            }
        }
        return $item;
    }
}