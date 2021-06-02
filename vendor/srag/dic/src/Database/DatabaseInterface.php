<?php

namespace srag\DIC\LiveVoting\Database;

use ilDBPdoInterface;
use ilDBStatement;

/**
 * Interface DatabaseInterface
 *
 * @package srag\DIC\LiveVoting\Database
 */
interface DatabaseInterface extends ilDBPdoInterface
{

    /**
     * Using MySQL native autoincrement for performance
     * Using PostgreSQL native sequence
     *
     * @param string $table_name
     * @param string $field
     */
    public function createAutoIncrement(string $table_name, string $field)/*: void*/ ;


    /**
     * @param string $table_name
     * @param array  $columns
     * @param array  $primary_columns
     */
    public function createOrUpdateTable(string $table_name, array $columns, array $primary_columns)/*: void*/ ;


    /**
     * Remove PostgreSQL native sequence table
     *
     * @param string $table_name
     */
    public function dropAutoIncrementTable(string $table_name)/*: void*/ ;


    /**
     * @param ilDBStatement $stm
     * @param callable      $callback
     *
     * @return object[]
     */
    public function fetchAllCallback(ilDBStatement $stm, callable $callback) : array;


    /**
     * @param ilDBStatement $stm
     * @param string        $class_name
     *
     * @return object[]
     */
    public function fetchAllClass(ilDBStatement $stm, string $class_name) : array;


    /**
     * @param ilDBStatement $stm
     * @param callable      $callback
     *
     * @return object|null
     */
    public function fetchObjectCallback(ilDBStatement $stm, callable $callback)/*:?object*/ ;


    /**
     * @param ilDBStatement $stm
     * @param string        $class_name
     *
     * @return object|null
     */
    public function fetchObjectClass(ilDBStatement $stm, string $class_name)/*:?object*/ ;


    /**
     * @param string $table_name
     * @param array  $columns
     * @param array  $values
     */
    public function multipleInsert(string $table_name, array $columns, array $values)/*:void*/ ;


    /**
     * Reset autoincrement
     *
     * @param string $table_name
     * @param string $field
     */
    public function resetAutoIncrement(string $table_name, string $field)/*: void*/ ;


    /**
     * @param string   $table_name
     * @param array    $values
     * @param string   $primary_key_field
     * @param int|null $primary_key_value
     *
     * @return int
     */
    public function store(string $table_name, array $values, string $primary_key_field,/*?*/ int $primary_key_value = 0) : int;
}
