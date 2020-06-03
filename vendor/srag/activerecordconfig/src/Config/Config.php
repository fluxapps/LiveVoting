<?php

namespace srag\ActiveRecordConfig\LiveVoting\Config;

use ActiveRecord;
use arConnector;
use LogicException;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class Config
 *
 * @package srag\ActiveRecordConfig\LiveVoting\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Config extends ActiveRecord
{

    use DICTrait;
    /**
     * @var string
     */
    const SQL_DATE_FORMAT = "Y-m-d H:i:s";
    /**
     * @var int
     */
    const TYPE_STRING = 1;
    /**
     * @var int
     */
    const TYPE_INTEGER = 2;
    /**
     * @var int
     */
    const TYPE_DOUBLE = 3;
    /**
     * @var int
     */
    const TYPE_BOOLEAN = 4;
    /**
     * @var int
     */
    const TYPE_TIMESTAMP = 5;
    /**
     * @var int
     */
    const TYPE_DATETIME = 6;
    /**
     * @var int
     */
    const TYPE_JSON = 7;
    /**
     * @var string
     */
    protected static $table_name;


    /**
     * @return string
     */
    public static function getTableName() : string
    {
        if (empty(self::$table_name)) {
            throw new LogicException("table name is empty - please call repository earlier!");
        }

        return self::$table_name;
    }


    /**
     * @param string $table_name
     */
    public static function setTableName(string $table_name)/* : void*/
    {
        self::$table_name = $table_name;
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::getTableName();
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::getTableName();
    }


    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      100
     * @con_is_notnull  true
     * @con_is_primary  true
     */
    protected $name = "";
    /**
     * @var mixed
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  false
     */
    protected $value = null;


    /**
     * Config constructor
     *
     * @param string|null      $primary_name_value
     * @param arConnector|null $connector
     */
    public function __construct(/*?string*/ $primary_name_value = null, /*?*/ arConnector $connector = null)
    {
        parent::__construct($primary_name_value, $connector);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            default:
                return null;
        }
    }


    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }


    /**
     * @param string $name
     */
    public function setName(string $name)/*: void*/
    {
        $this->name = $name;
    }


    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * @param mixed $value
     */
    public function setValue($value)/*: void*/
    {
        $this->value = $value;
    }
}
