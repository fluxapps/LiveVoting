<?php

namespace srag\ActiveRecordConfig;

use ActiveRecord;
use arConnector;
use arException;
use DateTime;
use srag\DIC\DICTrait;

/**
 * Class ActiveRecordConfig
 *
 * @package srag\ActiveRecordConfig
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ActiveRecordConfig extends ActiveRecord {

	use DICTrait;
	/**
	 * @var string
	 *
	 * @abstract
	 */
	const TABLE_NAME = "";
	/**
	 * @var string
	 *
	 * @access protected
	 */
	const SQL_DATE_FORMAT = "Y-m-d H:i:s";


	/**
	 * @return string
	 *
	 * @access protected
	 */
	public final function getConnectorContainerName()/*: string*/ {
		return static::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @access protected
	 *
	 * @deprecated
	 */
	public static final function returnDbTableName()/*: string*/ {
		return static::TABLE_NAME;
	}


	/**
	 * @param string $name
	 * @param bool   $store_new
	 *
	 * @return static
	 */
	protected static final function getConfig(/*string*/
		$name, /*bool*/
		$store_new = true)/*: static*/ {
		/**
		 * @var static $config
		 */

		$config = self::where([
			"name" => $name
		])->first();

		if ($config === NULL) {
			$config = new static();

			$config->setName($name);

			if ($store_new) {
				$config->store();
			}
		}

		return $config;
	}


	/**
	 * @param string     $name
	 * @param mixed|null $default_value
	 *
	 * @return mixed
	 */
	protected static final function getXValue(/*string*/
		$name, $default_value = NULL) {
		$config = self::getConfig($name);

		$value = $config->getValue();

		if ($value === NULL) {
			$value = $default_value;
		}

		return $value;
	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	protected static final function setXValue(/*string*/
		$name, $value)/*: void*/ {
		$config = self::getConfig($name, false);

		$config->setValue($value);

		$config->store();
	}


	/**
	 * Get all values
	 *
	 * @return string[] [ [ "name" => value ], ... ]
	 */
	protected static final function getValues()/*: array*/ {
		return array_reduce(self::get(), function (array $configs, self $config) {
			$configs[$config->getName()] = $config->getValue();

			return $configs;
		}, []);
	}


	/**
	 * Get all names
	 *
	 * @return string[] [ "name", ... ]
	 */
	protected static final function getNames()/*: array*/ {
		return array_keys(self::getValues());
	}


	/**
	 * Set all values
	 *
	 * @param array $configs       [ [ "name" => value ], ... ]
	 * @param bool  $remove_exists Delete all exists name before
	 */
	protected static final function setValues(array $configs, /*bool*/
		$remove_exists = false)/*: void*/ {
		if ($remove_exists) {
			self::truncateDB();
		}

		foreach ($configs as $name => $value) {
			self::setXValue($name, $value);
		}
	}


	/**
	 * Remove a name
	 *
	 * @param string $name Name
	 */
	protected static final function removeName(/*string*/
		$name)/*: void*/ {
		$config = self::getConfig($name, false);

		$config->delete();
	}


	/**
	 * @param string $name
	 * @param string $default_value
	 *
	 * @return string
	 */
	protected static final function getStringValue(/*string*/
		$name, /*string*/
		$default_value = "")/*: string*/ {
		return strval(self::getXValue($name, $default_value));
	}


	/**
	 * @param string $name
	 * @param string $value
	 */
	protected static final function setStringValue(/*string*/
		$name, $value)/*: void*/ {
		self::setXValue($name, strval($value));
	}


	/**
	 * @param string $name
	 * @param int    $default_value
	 *
	 * @return int
	 */
	protected static final function getIntegerValue(/*string*/
		$name, /*int*/
		$default_value = 0)/*: int*/ {
		return intval(self::getXValue($name, $default_value));
	}


	/**
	 * @param string $name
	 * @param int    $value
	 */
	protected static final function setIntegerValue(/*string*/
		$name, $value)/*: void*/ {
		self::setXValue($name, intval($value));
	}


	/**
	 * @param string $name
	 * @param double $default_value
	 *
	 * @return double
	 */
	protected static final function getDoubleValue(/*string*/
		$name, /*double*/
		$default_value = 0.0)/*: double*/ {
		return doubleval(self::getXValue($name, $default_value));
	}


	/**
	 * @param string $name
	 * @param double $value
	 */
	protected static final function setDoubleValue(/*string*/
		$name, $value)/*: void*/ {
		self::setXValue($name, doubleval($value));
	}


	/**
	 * @param string $name
	 * @param bool   $default_value
	 *
	 * @return bool
	 */
	protected static final function getBooleanValue(/*string*/
		$name, /*bool*/
		$default_value = false)/*: bool*/ {
		return boolval(filter_var(self::getXValue($name, $default_value), FILTER_VALIDATE_BOOLEAN));
	}


	/**
	 * @param string $name
	 * @param bool   $value
	 */
	protected static final function setBooleanValue(/*string*/
		$name, $value)/*: void*/ {
		self::setXValue($name, json_encode(boolval(filter_var($value, FILTER_VALIDATE_BOOLEAN))));
	}


	/**
	 * @param string $name
	 * @param int    $default_value
	 *
	 * @return int
	 */
	protected static final function getTimestampValue(/*string*/
		$name, /*int*/
		$default_value = 0)/*: int*/ {
		$value = self::getXValue($name);

		if ($value !== NULL) {
			$date_time = new DateTime($value);
		} else {
			$date_time = new DateTime("@" . $default_value);
		}

		return $date_time->getTimestamp();
	}


	/**
	 * @param string $name
	 * @param int    $value
	 */
	protected static final function setTimestampValue(/*string*/
		$name, $value)/*: void*/ {
		if ($value !== NULL) {
			$date_time = new DateTime("@" . $value);

			$formated = $date_time->format(self::SQL_DATE_FORMAT);

			self::setXValue($name, $formated);
		} else {
			// Fix `@null`
			self::setNullValue($name);
		}
	}


	/**
	 * @param string     $name
	 * @param bool       $assoc
	 * @param mixed|null $default_value
	 *
	 * @return mixed
	 */
	protected static final function getJsonValue(/*string*/
		$name, /*bool*/
		$assoc = false, $default_value = NULL) {
		return json_decode(self::getXValue($name, json_encode($default_value)), $assoc);
	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	protected static final function setJsonValue(/*string*/
		$name, $value)/*: void*/ {
		self::setXValue($name, json_encode($value));
	}


	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	protected static final function isNullValue(/*string*/
		$name)/*: bool*/ {
		return (self::getXValue($name) === NULL);
	}


	/**
	 * @param string $name
	 */
	protected static final function setNullValue(/*string*/
		$name)/*: void*/ {
		self::setXValue($name, NULL);
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
	protected $name = NULL;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  false
	 */
	protected $value = NULL;


	/**
	 * @return string
	 */
	protected final function getName()/*: string*/ {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	protected final function setName(/*string*/
		$name)/*: void*/ {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	protected final function getValue()/*: string*/ {
		return $this->value;
	}


	/**
	 * @param string $value
	 */
	protected final function setValue(/*string*/
		$value)/*: void*/ {
		$this->value = $value;
	}


	/**
	 * @param string $primary_key
	 * @param array  $add_constructor_args
	 *
	 * @return static
	 *
	 * @access protected
	 */
	public static final function find(/*string*/
		$primary_key, array $add_constructor_args = [])/*: static*/ {
		/**
		 * @var static $config
		 */

		$config = parent::find($primary_key, $add_constructor_args);

		return $config;
	}


	/**
	 * @param string $primary_key
	 * @param array  $add_constructor_args
	 *
	 * @return static
	 *
	 * @access protected
	 */
	public static final function findOrGetInstance(/*string*/
		$primary_key, array $add_constructor_args = [])/*: static*/ {
		/**
		 * @var static $config
		 */

		$config = parent::findOrGetInstance($primary_key, $add_constructor_args);

		return $config;
	}


	/**
	 * @param string $primary_key
	 * @param array  $add_constructor_args
	 *
	 * @return static
	 * @throws arException
	 *
	 * @access protected
	 */
	public static final function findOrFail(/*string*/
		$primary_key, array $add_constructor_args = [])/*: static*/ {
		/**
		 * @var static $config
		 */

		$config = parent::findOrFail($primary_key, $add_constructor_args);

		return $config;
	}


	/**
	 * ActiveRecordConfig constructor
	 *
	 * @param string|null      $primary_name_value
	 * @param arConnector|null $connector
	 */
	public final function __construct(/*?string*/
		$primary_name_value = NULL, /*?*/
		arConnector $connector = NULL) {
		parent::__construct($primary_name_value, $connector);
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 *
	 * @access protected
	 */
	public final function sleep(/*string*/
		$field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
			default:
				return NULL;
		}
	}


	/**
	 * @param string $field_name
	 * @param mixed  $field_value
	 *
	 * @return mixed|null
	 *
	 * @access protected
	 */
	public final function wakeUp(/*string*/
		$field_name, $field_value) {
		switch ($field_name) {
			default:
				return NULL;
		}
	}


	/**
	 * @throws arException
	 *
	 * @access protected
	 */
	public final function read()/*: void*/ {
		parent::read();
	}


	/**
	 * @access protected
	 */
	public final function afterObjectLoad()/*: void*/ {
		parent::afterObjectLoad();
	}


	/**
	 * @access protected
	 */
	public final function create()/*: void*/ {
		parent::create();
	}


	/**
	 * @access protected
	 */
	public final function update()/*: void*/ {
		parent::update();
	}


	/**
	 * @access protected
	 */
	public final function delete()/*: void*/ {
		parent::delete();
	}


	/**
	 * @access protected
	 */
	public final function store()/*: void*/ {
		parent::store();
	}


	/**
	 * @access     protected
	 *
	 * @deprecated Use store
	 */
	public final function save()/*: void*/ {
		parent::save();
	}


	/**
	 * @param string|null $new_id
	 *
	 * @return static
	 * @throws arException
	 *
	 * @access protected
	 */
	public final function copy(/*?string*/
		$new_id = NULL)/*: static*/ {
		/**
		 * @var static $config
		 */

		$config = parent::copy($new_id);

		return $config;
	}
}
