<?php

namespace Stratis\Component\Migrator\Task;

use Stratis\Component\Migrator\Migrator;
use Stratis\Component\Migrator\Configuration;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Reader\ReaderInterface;

/**
 * Class DefaultTask
 * @package Stratis\Component\Migrator\Task
 * @config array $sources
 */
class DefaultTask implements ReaderInterface
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $sources = array();

    /**
     * DefaultTask constructor.
     * Crawl sources configuration and get data from those readers
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        // Get sources
        $sources = $config->get(array('sources'), array());

        foreach ($sources as $key => $source) {

            // Skip this source if not defined
            if (!is_array($source) || empty($source)) {
                continue;
            }

            // Empty data array
            $data = array();

            // Create reader with source conf
            $reader = Migrator::createReader(new Configuration($source));

            // Writer will set data in an array
            $writer = new CallbackWriter(function ($item) use (&$data) {
                array_push($data, $item);
            });

            // Create new workflow
            $workflow = new Workflow($reader);
            $workflow->addWriter($writer);
            $workflow->process();

            // Apply data to sources
            $this->sources[$key] = $data;
        }

        if (count($this->sources) > 0) {
            $this->data = current($this->sources);
        }

        // Exec main with this task config
        $this->main($config->export());
    }

    /**
     * @param Configuration $config
     */
    public function main(Configuration $config)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return array_keys(
            $this->current()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        $key = key($this->data);

        return ($key !== null && $key !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->data);
    }
}