<?php

namespace Stratis\Component\Migrator\Reader;
use Stratis\Component\Migrator\Configuration;

/**
 * Class PdoReader
 * @package Stratis\Component\Migrator\Reader
 *
 * @config string $table
 * @config string $query
 */
class PdoReader extends \Ddeboer\DataImport\Reader\PdoReader
{
    /**
     * PdoReader constructor.
     * @param Configuration $config
     * @throws \Exception
     */
    public function __construct(Configuration $config)
    {
        $table = $config->get(array('table'), '');
        $query = $config->get(array('query'), '');

        if (strlen($table) == 0) {
            throw new \Exception('[PdoReader] Table name is not defined');
        }

        if (strlen($query) == 0) {
            $query = 'SELECT * FROM ' . $table;
        }

        $db = new \medoo($config->get());
        parent::__construct($db->pdo, $query);
    }
}
