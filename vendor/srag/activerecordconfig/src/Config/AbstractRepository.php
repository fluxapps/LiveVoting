<?php

namespace srag\ActiveRecordConfig\LiveVoting\Config;

use ilDateTime;
use ilDateTimeException;
use LogicException;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class AbstractRepository
 *
 * @package srag\ActiveRecordConfig\LiveVoting\Config
 */
abstract class AbstractRepository
{

    use DICTrait;

    /**
     * AbstractRepository constructor
     */
    protected function __construct()
    {
        Config::setTableName($this->getTableName());
    }


    /**
     *
     */
    public function dropTables() : void
    {
        self::dic()->database()->dropTable(Config::getTableName(), false);
    }


    /**
     * @return AbstractFactory
     */
    public abstract function factory() : AbstractFactory;


    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getValue(string $name)
    {
        if (isset($this->getFields()[$name])) {
            $field = $this->getFields()[$name];
            if (!is_array($field)) {
                $field = [$field];
            }

            $type = $field[0];

            $default_value = $field[1];

            switch ($type) {
                case Config::TYPE_STRING:
                    return $this->getStringValue($name, $default_value);

                case Config::TYPE_INTEGER:
                    return $this->getIntegerValue($name, $default_value);

                case Config::TYPE_DOUBLE:
                    return $this->getFloatValue($name, $default_value);

                case Config::TYPE_BOOLEAN:
                    return $this->getBooleanValue($name, $default_value);

                case Config::TYPE_TIMESTAMP:
                    return $this->getTimestampValue($name, $default_value);

                case Config::TYPE_DATETIME:
                    return $this->getDateTimeValue($name, $default_value);

                case Config::TYPE_JSON:
                    $assoc = boolval($field[2]);

                    return $this->getJsonValue($name, $assoc, $default_value);

                default:
                    throw new LogicException("Invalid type $type!");
                    break;
            }
        }

        throw new LogicException("Invalid field $name!");
    }


    /**
     * @return array
     */
    public function getValues() : array
    {
        $values = [];

        foreach ($this->getFields() as $name) {
            $values[$name] = $this->getValue($name);
        }

        return $values;
    }


    /**
     *
     */
    public function installTables() : void
    {
        Config::updateDB();
    }


    /**
     * @param string $name
     */
    public function removeValue(string $name) : void
    {
        $config = $this->getConfig($name, false);

        $this->deleteConfig($config);
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setValue(string $name, $value) : void
    {
        if (isset($this->getFields()[$name])) {
            $field = $this->getFields()[$name];
            if (!is_array($field)) {
                $field = [$field];
            }

            $type = $field[0];

            switch ($type) {
                case Config::TYPE_STRING:
                    $this->setStringValue($name, $value);

                    return;

                case Config::TYPE_INTEGER:
                    $this->setIntegerValue($name, $value);

                    return;

                case Config::TYPE_DOUBLE:
                    $this->setFloatValue($name, $value);

                    return;

                case Config::TYPE_BOOLEAN:
                    $this->setBooleanValue($name, $value);

                    return;

                case Config::TYPE_TIMESTAMP:
                    $this->setTimestampValue($name, $value);

                    return;

                case Config::TYPE_DATETIME:
                    $this->setDateTimeValue($name, $value);

                    return;

                case Config::TYPE_JSON:
                    $this->setJsonValue($name, $value);

                    return;

                default:
                    throw new LogicException("Invalid type $type!");
                    break;
            }
        }

        throw new LogicException("Invalid field $name!");
    }


    /**
     * @param array $values
     * @param bool  $remove_exists
     */
    public function setValues(array $values, bool $remove_exists = false) : void
    {
        if ($remove_exists) {
            Config::truncateDB();
        }

        foreach ($values as $name => $value) {
            $this->setValue($name, $value);
        }
    }


    /**
     * @param Config $config
     */
    protected function deleteConfig(Config $config) : void
    {
        $config->delete();
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return bool
     */
    protected function getBooleanValue(string $name, $default_value = false) : bool
    {
        return boolval(filter_var($this->getXValue($name, $default_value), FILTER_VALIDATE_BOOLEAN));
    }


    /**
     * @param string $name
     * @param bool   $store_new
     *
     * @return Config
     */
    protected function getConfig(string $name, bool $store_new = true) : Config
    {
        /**
         * @var Config $config
         */

        $config = Config::where([
            "name" => $name
        ])->first();

        if ($config === null) {
            $config = $this->factory()->newInstance();

            $config->setName($name);

            if ($store_new) {
                $this->storeConfig($config);
            }
        }

        return $config;
    }


    /**
     * @param string          $name
     * @param ilDateTime|null $default_value
     *
     * @return ilDateTime|null
     */
    protected function getDateTimeValue(string $name, /*?*/ ilDateTime $default_value = null) : ?ilDateTime
    {
        $value = $this->getXValue($name);

        if ($value !== null) {
            try {
                $value = new ilDateTime(IL_CAL_DATETIME, $value);
            } catch (ilDateTimeException $ex) {
                $value = $default_value;
            }
        } else {
            $value = $default_value;
        }

        return $value;
    }


    /**
     * @return array
     */
    protected abstract function getFields() : array;


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return float
     */
    protected function getFloatValue(string $name, $default_value = 0.0) : float
    {
        return floatval($this->getXValue($name, $default_value));
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return int
     */
    protected function getIntegerValue(string $name, $default_value = 0) : int
    {
        return intval($this->getXValue($name, $default_value));
    }


    /**
     * @param string $name
     * @param bool   $assoc
     * @param mixed  $default_value
     *
     * @return mixed
     */
    protected function getJsonValue(string $name, bool $assoc = false, $default_value = null)
    {
        return json_decode($this->getXValue($name, json_encode($default_value)), $assoc);
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return string
     */
    protected function getStringValue(string $name, $default_value = "") : string
    {
        return strval($this->getXValue($name, $default_value));
    }


    /**
     * @return string
     */
    protected abstract function getTableName() : string;


    /**
     * @param string $name
     * @param int    $default_value
     *
     * @return int
     */
    protected function getTimestampValue(string $name, int $default_value = 0) : int
    {
        $value = $this->getDateTimeValue($name);

        if ($value !== null) {
            return $value->getUnixTime();
        } else {
            return $default_value;
        }
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return mixed
     */
    protected function getXValue(string $name, $default_value = null)
    {
        $config = $this->getConfig($name);

        $value = $config->getValue();

        if ($value === null) {
            $value = $default_value;
        }

        return $value;
    }


    /**
     * @param string $name
     *
     * @return bool
     */
    protected function isNullValue(string $name) : bool
    {
        return ($this->getXValue($name) === null);
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setBooleanValue(string $name, $value) : void
    {
        $this->setXValue($name, json_encode(boolval(filter_var($value, FILTER_VALIDATE_BOOLEAN))));
    }


    /**
     * @param string          $name
     * @param ilDateTime|null $value
     */
    protected function setDateTimeValue(string $name, /*?*/ ilDateTime $value = null) : void
    {
        if ($value !== null) {
            $this->setXValue($name, $value->get(IL_CAL_DATETIME));
        } else {
            $this->setNullValue($name);
        }
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setFloatValue(string $name, $value) : void
    {
        $this->setXValue($name, floatval($value));
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setIntegerValue(string $name, $value) : void
    {
        $this->setXValue($name, intval($value));
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setJsonValue(string $name, $value) : void
    {
        $this->setXValue($name, json_encode($value));
    }


    /**
     * @param string $name
     */
    protected function setNullValue(string $name) : void
    {
        $this->setXValue($name, null);
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setStringValue(string $name, $value) : void
    {
        $this->setXValue($name, strval($value));
    }


    /**
     * @param string $name
     * @param int    $value
     */
    protected function setTimestampValue(string $name, int $value) : void
    {
        if ($value !== null) {
            try {
                $this->setDateTimeValue($name, new ilDateTime(IL_CAL_UNIX, $value));
            } catch (ilDateTimeException $ex) {
            }
        } else {
            // Fix `@null`
            $this->setNullValue($name);
        }
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setXValue(string $name, $value) : void
    {
        $config = $this->getConfig($name, false);

        $config->setValue($value);

        $this->storeConfig($config);
    }


    /**
     * @param Config $config
     */
    protected function storeConfig(Config $config) : void
    {
        $config->store();
    }
}
