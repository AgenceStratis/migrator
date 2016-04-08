<?php

namespace Stratis\Component\Migrator\Writer;

use Ddeboer\DataImport\Writer;
use Ddeboer\DataImport\Exception\WriterException;
use Stratis\Component\Migrator\Configuration;

// TODO: Fix class and insert modes

/**
 * Class DbWriter
 * @package Stratis\Component\Migrator\Writer
 *
 * @config string $host
 * @config string $dbname
 * @config string $dbtype
 * @config string $username
 * @config string $password
 * @config string $table
 * @config string $insert_mode
 */
class DbWriter extends \Ddeboer\DataImport\Writer\PdoWriter
{
    /**
     * @var string
     */
    protected $insertMode;


    /**
     * DbWriter constructor.
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $host       = $config->get(array('host'), 'localhost');
        $dbname     = $config->get(array('dbname'), '');
        $dbtype     = $config->get(array('dbtype'), 'mysql');
        $username   = $config->get(array('username'), 'root');
        $password   = $config->get(array('password'), '');
        $table      = $config->get(array('table'), '');

        $insertMode = $config->get(array('insert_mode'), 'insert');

        $pdo = new \PDO($dbtype . ':host=' . $host . ';dbname=' . $dbname, $username, $password);
        parent::__construct($pdo, $table);

        $this->insertMode = strtolower($insertMode);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
    }

    /**
     * Create Statement using mode value
     * @param array $item
     * @return string
     */
    protected function createStatement(array $item)
    {
        $keys = implode(',', array_keys($item));
        $values = substr(str_repeat('?,', count($item)), 0, -1);
        $statement = '';

        switch ($this->insertMode) {

            // insert values and reset it if primary key is matched
            case 'replace': {
                $statement = "REPLACE INTO " . $this->tableName . " (" . $keys . ") VALUES (" . $values . ")";
                break;
            }

            // insert values if it doesn't exist
            // useful for tables without primary key
            case 'not_exists': {

                $where = implode(" AND ", array_map(
                    function ($value, $key) {
                        return $key . "='" . $value . "'";
                    },
                    $item,
                    array_keys($item)
                ));

                $statement = "INSERT INTO " . $this->tableName . " (" . $keys . ") SELECT '" . implode("','",
                        array_values($item))
                    . "' FROM DUAL WHERE NOT EXISTS ( SELECT * FROM " . $this->tableName
                    . " WHERE " . $where . " ) LIMIT 1";
                break;
            }

            // classic insert, may cause errors if primary key is specified
            case 'insert':
            default: {
                $statement = "INSERT INTO " . $this->tableName . " (" . $keys . ") VALUES (" . $values . ")";
            }
        }

        // var_dump($statement); die;

        return $statement;
    }

    /**
     * @param array $item
     * @throws WriterException
     * @return void
     */
    public function writeItem(array $item)
    {
        try {

            $this->statement = $this->pdo->prepare(
                $this->createStatement($item)
            );

            //for PDO objects that do not have exceptions enabled
            if (!$this->statement) {
                throw new WriterException('Failed to prepare write statement for item: ' . implode(',', $item));
            }

            //do the insert
            if (!$this->statement->execute(array_values($item))) {

                $this->pdo->errorInfo();
                var_dump($item);
                die;

                // throw new WriterException('Failed to write item: '.implode(',', $item));
            }

        } catch (\Exception $e) {
            //convert exception so the abstracton doesn't leak
            throw new WriterException('Write failed (' . $e->getMessage() . ').', null, $e);
        }
    }
}
