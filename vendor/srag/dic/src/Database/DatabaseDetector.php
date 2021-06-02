<?php

namespace srag\DIC\LiveVoting\Database;

use ilDBConstants;
use ilDBInterface;
use ilDBPdoInterface;
use ilDBPdoPostgreSQL;
use ilDBStatement;
use PDO;
use srag\DIC\LiveVoting\Exception\DICException;
use stdClass;

/**
 * Class DatabaseDetector
 *
 * @package srag\DIC\LiveVoting\Database
 */
class DatabaseDetector extends AbstractILIASDatabaseDetector
{

    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @param ilDBInterface $db
     *
     * @return self
     *
     * @throws DICException DatabaseDetector only supports ilDBPdoInterface!
     */
    public static function getInstance(ilDBInterface $db) : self
    {
        if (!($db instanceof ilDBPdoInterface)) {
            throw new DICException("DatabaseDetector only supports ilDBPdoInterface!");
        }

        if (self::$instance === null) {
            self::$instance = new self($db);
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     */
    public function createAutoIncrement(string $table_name, string $field)/*: void*/
    {
        $table_name_q = $this->quoteIdentifier($table_name);
        $field_q = $this->quoteIdentifier($field);
        $seq_name = $table_name . "_seq";
        $seq_name_q = $this->quoteIdentifier($seq_name);

        switch (true) {
            case($this->db instanceof ilDBPdoPostgreSQL):
                $this->manipulate('CREATE SEQUENCE IF NOT EXISTS ' . $seq_name_q);

                $this->manipulate('ALTER TABLE ' . $table_name_q . ' ALTER COLUMN ' . $field_q . ' TYPE INT, ALTER COLUMN ' . $field_q
                    . ' SET NOT NULL, ALTER COLUMN ' . $field_q . ' SET DEFAULT nextval(' . $seq_name_q . ')');
                break;

            default:
                $this->manipulate('ALTER TABLE ' . $table_name_q . ' MODIFY COLUMN ' . $field_q . ' INT NOT NULL AUTO_INCREMENT');
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function createOrUpdateTable(string $table_name, array $columns, array $primary_columns)/*: void*/
    {
        if (!$this->tableExists($table_name)) {
            $this->createTable($table_name, $columns);
            if (!empty($primary_columns)) {
                $this->addPrimaryKey($table_name, $primary_columns);
            }
        } else {
            foreach ($columns as $column_name => $column) {
                if (!$this->tableColumnExists($table_name, $column_name)) {
                    $this->addTableColumn($table_name, $column_name, $column);
                }
            }
        }
    }


    /**
     * @inheritDoc
     */
    public function dropAutoIncrementTable(string $table_name)/*: void*/
    {
        $seq_name = $table_name . "_seq";
        $seq_name_q = $this->quoteIdentifier($seq_name);

        switch (true) {
            case($this->db instanceof ilDBPdoPostgreSQL):
                $this->manipulate('DROP SEQUENCE IF EXISTS ' . $seq_name_q);
                break;

            default:
                // Nothing to do in MySQL
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function fetchAllCallback(ilDBStatement $stm, callable $callback) : array
    {
        return array_map($callback, $this->fetchAllClass($stm, stdClass::class));
    }


    /**
     * @inheritDoc
     */
    public function fetchAllClass(ilDBStatement $stm, string $class_name) : array
    {
        return PdoStatementContextHelper::getPdoStatement($stm)->fetchAll(PDO::FETCH_CLASS, $class_name);
    }


    /**
     * @inheritDoc
     */
    public function fetchObjectCallback(ilDBStatement $stm, callable $callback)/*:?object*/
    {
        $data = $this->fetchObjectClass($stm, stdClass::class);

        if ($data !== null) {
            return $callback($data);
        } else {
            return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function fetchObjectClass(ilDBStatement $stm, string $class_name)/*:?object*/
    {
        $data = PdoStatementContextHelper::getPdoStatement($stm)->fetchObject($class_name);

        if ($data !== false) {
            return $data;
        } else {
            return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function multipleInsert(string $table_name, array $columns, array $values)/*:void*/
    {
        if (empty($columns) || empty($values)) {
            return;
        }

        $this->manipulate('INSERT INTO ' . $this->quoteIdentifier($table_name) . ' (' . implode(',', $columns) . ') VALUES ' . implode(',', array_map(function (array $values2) : string {
                return '(' . implode(',', array_map(function (array $value) : string {
                        return $this->quote($value[0], $value[1]);
                    }, $values2)) . ')';
            }, $values)));
    }


    /**
     * @inheritDoc
     */
    public function resetAutoIncrement(string $table_name, string $field)/*: void*/
    {
        $table_name_q = $this->quoteIdentifier($table_name);
        $field_q = $this->quoteIdentifier($field);

        switch (true) {
            case($this->db instanceof ilDBPdoPostgreSQL):
                $this->manipulate('SELECT setval(' . $table_name_q . ', (SELECT MAX(' . $field_q . ') FROM ' . $table_name_q . '))');
                break;

            default:
                $this->manipulate('ALTER TABLE ' . $table_name_q
                    . ' AUTO_INCREMENT=1'); // 1 has the effect MySQL will automatic calculate next max id
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function store(string $table_name, array $values, string $primary_key_field,/*?*/ int $primary_key_value = 0) : int
    {
        if (empty($primary_key_value)) {
            $this->insert($table_name, $values);

            return $this->getLastInsertId();
        } else {
            $this->update($table_name, $values, [
                $primary_key_field => [ilDBConstants::T_INTEGER, $primary_key_value]
            ]);

            return $primary_key_value;
        }
    }
}
