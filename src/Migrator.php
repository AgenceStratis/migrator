<?php

namespace Stratis\Component\Migrator;

use Stratis\Component\Migrator\Converter\Processor;
use Stratis\Component\Migrator\Converter\Mapping;
use Symfony\Component\Yaml\Yaml;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Filter\OffsetFilter;

/**
 * Class Migrator
 * @package Stratis\Component\Migrator
 *
 * Importer/Exporter for multiples data sources
 *
 * Usage:
 *  $migrator = new Migrator('config.yml');
 *  $migrator->process();
 */
class Migrator extends Workflow
{
    /**
     * @var int
     */
    const version = 9.6;

    /**
     * @var array
     */
    protected $conf = array();

    /**
     * Migrator constructor.
     * @param string|array $fileset
     * @param $logger
     */
    public function __construct($fileset, $logger = null)
    {
        // Load configuration file(s)
        $this->loadConf($fileset);

        if (array_key_exists('source', $this->conf)) {

            // Set sources if it does not exist
            if (!array_key_exists('sources', $this->conf)) {
                $this->conf['sources'] = array();
            }

            // Move single source into sources list
            $this->conf['sources'][] = $this->conf['source'];
        }

        // Create configuration object
        $this->conf = new Configuration($this->conf);

        // Init workflow
        parent::__construct($this->createTask(), $logger);
        $this->addWriter($this->createWriter());

        // Starting line and number of items to get
        $this->addFilter(
            new OffsetFilter(
                $this->conf->get(array('options', 'offset'), 0),
                $this->conf->get(array('options', 'count'), null)
            )
        );

        // Add processors to the workflow
        $processor = $this->conf->get(array('processors'), array());

        if (is_array($processor) && !empty($processor)) {
            $this->addItemConverter(new Processor($processor));
        }

        // Add mapping to the workflow
        $mapping = $this->conf->get(array('mapping'), array());

        if (is_array($mapping) && !empty($mapping)) {
            $this->addItemConverter(new Mapping($mapping));
        }
    }

    /**
     * Load configuration file
     * @param $fileName
     * @throws \Exception
     */
    protected function loadConf($fileName)
    {
        if (is_array($fileName)) {
            foreach ($fileName as $subFile) {
                $this->loadConf($subFile);
            }
            return;
        }

        if (!file_exists($fileName)) {
            throw new \Exception('Configuration file does not exist');
        }

        $conf = Yaml::parse(
            file_get_contents($fileName)
        );

        // execute requires
        if (array_key_exists('require', $conf)) {

            $require = $conf['require'];
            $reqPath = dirname($fileName) . '/';

            if (is_array($require) && count($require) > 0) {
                foreach ($require as $reqFile) {
                    $this->loadConf($reqPath . $reqFile);
                }
            }

            if (is_string($require) && strlen($require) > 0) {
                $this->loadConf($reqPath . $require);
            }
        }

        // merge configurations
        $this->conf = array_merge_recursive_distinct($this->conf, $conf);
    }

    /**
     * Create an object with a Configuration as parameter
     * Can be used to create Readers, Writers and Tasks
     *
     * Examples:
     *  Migrator::summon(array('Reader', 'Csv'), $config);
     *  Migrator::summon(array('Writer', 'Json'), $config);
     *  Migrator::summon(array('Task', 'Typo3', 'MM'), $config);
     *
     * @param array $namespace
     * @param Configuration $config
     * @return mixed
     * @throws \Exception
     */
    public static function summon(array $namespace, Configuration $config)
    {
        // Set namespace as camel case
        $namespace = array_values(
            array_map('ucwords',
                array_map('strtolower', $namespace)
            )
        );

        // Pop prefix from namespace
        $prefix = array_pop($namespace);

        // No type specified in namespace
        if ($prefix == null || strlen($prefix) == 0) {
            throw new \Exception('Type not found in ' . implode('\\', $namespace));
        }

        // Build class name from type
        $class = 'Stratis\Component\Migrator\\' . implode('\\', $namespace) . '\\' . $prefix . $namespace[0];

        // Check if class exists
        if (!class_exists($class)) {
            throw new \Exception($class . ' does not exists');
        }

        // Return built lexer with params
        return new $class($config);
    }

    /**
     * Create a Reader with given Configuration object
     * @param Configuration $config
     * @return mixed
     * @throws \Exception
     */
    public static function createReader(Configuration $config) {
        $name = $config->get(array('type'));
        return Migrator::summon(array('Reader', $name), $config);
    }

    /**
     * Create Writer from config
     * @return mixed
     * @throws \Exception
     */
    protected function createWriter() {
        $name   = $this->conf->get(array('dest', 'type'));
        $config = $this->conf->export(array('dest'));
        return Migrator::summon(array('Writer', $name), $config);
    }

    /**
     * Create Task from config
     * @return mixed
     * @throws \Exception
     */
    protected function createTask()
    {
        // Split name and prepend Task namespace to it
        $name = $this->conf->get(array('task', 'type'), 'default');
        $namespace = array_merge(array('Task'), explode('/', $name));

        // Summon a Task with the current Migrator config
        return Migrator::summon(
            $namespace,
            $this->conf->export()
        );
    }

    /**
     * Show version in CLI env
     * @return string
     */
    public static function showVersion()
    {
        echo "Migrator version: " . Migrator::version . "\n",
        "Copyright (c) 2016 Stratis\n";
        die;
    }

    /**
     * Show usage in CLI env
     * @return string
     */
    public static function showUsage()
    {
        echo "\nUsage:\n",
        " migrator.phar [files...]\n\n",
        "Options:\n",
        " -h, --help \t\tThis small usage guide\n",
        " -v, --version \t\tOutput version information\n\n";
        die;
    }
}
