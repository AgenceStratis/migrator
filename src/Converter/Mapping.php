<?php

namespace Stratis\Component\Migrator\Converter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

/**
 * Class Mapping
 * @package Stratis\Component\Migrator\Converter
 */
class Mapping implements ItemConverterInterface
{
    /**
     * @var array
     */
    private $route = array();

    /**
     * Mapping constructor.
     * Parse config into route array
     * $route[actualKey] = newKey
     *
     * If you want to delete a column (in your yaml config)
     * You can use "field:" or "field: ''"
     *
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        foreach ($mapping as $item) {
            if (is_array($item)) {

                // New route
                $key = key($item);
                $newKey = current($item);

                if (strlen($newKey) > 0) {
                    // Setup new route
                    $this->route[$key] = $newKey;
                } else {
                    // Ignore column
                    unset($this->route[$key]);
                }
            } elseif (is_string($item)) {
                // Use field as it is
                $this->route[$item] = $item;
            }
        }
    }

    /**
     * @param mixed $item
     * @return array
     */
    public function convert($item)
    {
        $routedItem = array();

        // Apply custom route to the item
        foreach ($this->route as $key => $newKey) {
            if (array_key_exists($key, $item)) {
                $routedItem[$newKey] = $item[$key];
            }
        }

        return $routedItem;
    }
}