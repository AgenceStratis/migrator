<?php

namespace Stratis\Component\Migrator\Writer;

use Ddeboer\DataImport\Writer;
use Ddeboer\DataImport\Exception\WriterException;
use Stratis\Component\Migrator\Configuration;

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
     * @var array
     */
    protected $uniqueFields;

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

        // Array containing fields that need to be checked to avoid duplicate data
        $this->uniqueFields = $config->get(array('unique'), array());

        // Create PDO object from config
        $pdo = new \PDO($dbtype . ':host=' . $host . ';dbname=' . $dbname, $username, $password);

        // Set PDO error modes (for error output)
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);

        parent::__construct($pdo, $table);
    }

    /**
     * @param $statement
     * @param array $item
     * @return mixed
     */
    protected function query($statement, array $item)
    {
        // Prepare statement
        $query = $this->pdo->prepare($statement);

        // Execute with item values
        if (!$query->execute(array_values($item))) {

            // Output errors
            $this->pdo->errorInfo();
            var_dump($item);
            die;
        }

        // Return query
        return $query;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function keys(array $item)
    {
        return implode(',', array_keys($item));
    }

    /**
     * @param array $item
     * @return string
     */
    protected function values(array $item)
    {
        return implode(',', array_values($item));
    }

    /**
     * @param array $values
     * @param string $delimiter
     * @return string
     */
    protected function condition(array $values, $delimiter = ',')
    {
        return implode($delimiter,
            array_map(
                function ($key) {
                    return $key . '=?';
                },
                array_keys($values)
            )
        );
    }

    /**
     * @param array $values
     * @return string
     */
    protected function where(array $values)
    {
        $where = $this->condition($values, ' AND ');
        return strlen($where) ? ' WHERE ' . $where : '';
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function itemExists(array $item)
    {
        // Get values from unique constraint
        $values = array_intersect_key(
            $item, array_flip($this->uniqueFields)
        );

        $query = $this->query(
            "SELECT * FROM " . $this->tableName . $this->where($values), $values
        );

        return $query->rowCount() > 0;
    }

    /**
     * @param array $item
     */
    protected function insertItem(array $item)
    {
        // Replace values by question marks
        $values = substr(str_repeat('?,', count($item)), 0, -1);

        // Build query
        $statement = "INSERT INTO " . $this->tableName . "(" . $this->keys($item) . ") VALUES(" . $values . ")";

        // Execute insert
        $this->query($statement, $item);
    }

    /**
     * @param array $item
     */
    protected function updateItem(array $item)
    {
        // Get values from unique constraint
        $unique = array_intersect_key(
            $item, array_flip($this->uniqueFields)
        );

        // Get elements to update
        $update = array_diff_key(
            $item, array_flip($this->uniqueFields)
        );

        // Execute query if there are elements to update
        if (count($update) > 0) {

            // Build query string
            $statement = "UPDATE " . $this->tableName . " SET " . $this->condition($update) . $this->where($unique);

            // Execute query with merged args
            $this->query($statement, array_merge($update, $unique));
        }
    }

    /**
     * @param array $item
     * @throws WriterException
     * @return void
     */
    public function writeItem(array $item)
    {
        try {
            if ($this->itemExists($item)) {
                $this->updateItem($item);
            } else {
                $this->insertItem($item);
            }
        } catch (\Exception $e) {
            //convert exception so the abstraction doesn't leak
            throw new WriterException('Write failed (' . $e->getMessage() . ').', null, $e);
        }
    }
}
