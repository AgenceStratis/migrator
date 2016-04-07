<?php

namespace Stratis\Component\Migrator\Writer;

use Stratis\Component\Migrator\Configuration;
use Ddeboer\DataImport\Writer\AbstractStreamWriter;

/**
 * Class JsonWriter
 * @package Stratis\Component\Migrator\Writer
 *
 * @config bool $pretty
 * @config bool $unicode
 */
class JsonWriter extends AbstractStreamWriter
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * Options flags
     * @var bool
     */
    private $options = 0;

    /**
     * JsonWriter constructor.
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        parent::__construct();

        if ($config->get(array('pretty')) == true) {
            $this->options |= JSON_PRETTY_PRINT;
        }

        if ($config->get(array('unicode')) == true) {
            $this->options |= JSON_UNESCAPED_UNICODE;
        }
    }

    /**
     * Open an array
     */
    public function prepare()
    {
        fwrite($this->getStream(), "[");
    }

    /**
     * Encode items to JSON and append to the file
     * @param array $item
     * @return void
     */
    public function writeItem(array $item)
    {
        if ($this->count++ > 0) {
            fwrite($this->getStream(), ",\n");
        }

        fwrite(
            $this->getStream(),
            json_encode($item, $this->options)
        );
    }

    /**
     * End of the file
     */
    public function finish()
    {
        fwrite($this->getStream(), "]\n");
    }
}
