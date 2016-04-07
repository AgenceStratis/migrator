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
 *    $migrator = new Migrator('config.yml');
 *    $migrator->process();
 */
class Migrator extends Workflow
{
    /**
     * @var int
     */
    const version = 3;

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

        // Create configuration object
        $this->conf = new Configuration($this->conf);

        // Create reader object
        $reader = $this->createLexer(
            $this->conf->export(array('source')),
            Migrator::READER
        );

        // Create writer
        $writer = $this->createLexer(
            $this->conf->export(array('dest')),
            Migrator::WRITER
        );

        // init workflow
        parent::__construct($reader, $logger);
        $this->addWriter($writer);

        // Starting line and number of items to get
        $this->addFilter(
            new OffsetFilter(
                $this->conf->get(array('options', 'offset'), 0),
                $this->conf->get(array('options', 'count'), null)
            )
        );

        // Add processors to the workflow
        $this->addItemConverter(
            new Processor(
                $this->conf->get(array('processors'), array())
            )
        );

        // Add mapping to the workflow
        $this->addItemConverter(
            new Mapping(
                $this->conf->get(array('mapping'), array())
            )
        );
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
     * Create a reader or a writer object, according to given data type
     * Lexer type can be: Migrator::READER or Migrator::WRITER
     */
    const READER = 'Reader';
    const WRITER = 'Writer';

    /**
     * @param Configuration $config
     * @param $lexer
     * @return mixed
     * @throws \Exception
     */
    protected function createLexer($config, $lexer)
    {
        // Get type from config and format it (camel case)
        $type = $config->get(array('type'));

        // No type specified
        if ($type == null || strlen($type) == 0) {
            throw new \Exception('No data type found');
        }

        // Build class name from type
        $class = 'Stratis\Component\Migrator\\' . $lexer . '\\' . ucwords(strtolower($type)) . $lexer;

        // Check if class exists
        if (!class_exists($class)) {
            throw new \Exception($class . ' does not exists');
        }

        // Return built lexer with params
        return new $class($config);
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
