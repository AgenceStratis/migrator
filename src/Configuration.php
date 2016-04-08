<?php

namespace Stratis\Component\Migrator;

/**
 * Class Configuration
 * @package Stratis\Component\Migrator
 */
class Configuration
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * Configuration constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array $search
     * @param mixed $default
     * @return array|null
     */
    public function get(array $search, $default = null)
    {
        // Set current position at data root
        $current = $this->data;

        // Crawl data, following $search path
        foreach ($search as $key) {
            if (array_key_exists($key, $current)) {

                // If path exists, go to the next node
                $current = $current[$key];

            } else {

                // Node not found, cancel search and return default (null)
                $current = $default;
                break;
            }
        }

        return $current;
    }

    /**
     * @param array $search
     * @return Configuration
     */
    public function export(array $search = array())
    {
        return new Configuration(
            $this->get($search, array())
        );
    }
}
