<?php

namespace Stratis\Component\Migrator\Reader;

use Stratis\Component\Migrator\Configuration;

/**
 * Class DbReader
 * @package Stratis\Component\Migrator\Reader
 *
 * @config string $host
 * @config string $dbname
 * @config string $dbtype
 * @config string $username
 * @config string $password
 * @config string $table
 * @config string $query
 */
class DbReader extends \Ddeboer\DataImport\Reader\PdoReader
{
    /**
     * PdoReader constructor.
     * @param Configuration $config
     * @throws \Exception
     */
    public function __construct(Configuration $config)
    {
        $host       = $config->get(array('host'), 'localhost');
        $dbname     = $config->get(array('dbname'), '');
        $dbtype     = $config->get(array('dbtype'), 'mysql');
        $username   = $config->get(array('username'), 'root');
        $password   = $config->get(array('password'), '');
        $table      = $config->get(array('table'), '');
        $query      = $config->get(array('query'), '');

        if ($table . $query == '') {
            throw new \Exception('Table name is not defined');
        }

        if ($query == '') {
            $query = 'SELECT * FROM ' . $table;
        }

        $pdo = new \PDO($dbtype . ':host=' . $host . ';dbname=' . $dbname, $username, $password);
        parent::__construct($pdo, $query);
    }
}
