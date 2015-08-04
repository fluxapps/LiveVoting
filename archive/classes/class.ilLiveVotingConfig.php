<?php

/**
 * ilLiveVotingConfig
 *
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version $Id$
 */
class ilLiveVotingConfig {

	/**
	 * @var string
	 */
	protected $table_name = '';
	/**
	 * @var array
	 */
	protected static $cache = array();


	/**
	 * @param $table_name
	 */
	function __construct($table_name) {
		$this->table_name = $table_name;
	}


	/**
	 * @param string $table_name
	 */
	public function setTableName($table_name) {
		$this->table_name = $table_name;
	}


	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->table_name;
	}


	/**
	 * @param $method
	 * @param $params
	 *
	 * @return bool|null
	 */
	function __call($method, $params) {
		if (substr($method, 0, 3) == 'get') {
			return $this->getValue(self::_fromCamelCase(substr($method, 3)));
		} else {
			if (substr($method, 0, 3) == 'set') {
				$this->setValue(self::_fromCamelCase(substr($method, 3)), $params[0]);

				return true;
			} else {
				return NULL;
			}
		}
	}


	/**
	 * @param $key
	 * @param $value
	 */
	public function setValue($key, $value) {
		global $ilDB;
		if (! is_string($this->getValue($key))) {
			$ilDB->insert($this->getTableName(), array(
				"config_key" => array(
					"text",
					$key
				),
				"config_value" => array(
					"text",
					$value
				)
			));
		} else {
			$ilDB->update($this->getTableName(), array(
				"config_key" => array(
					"text",
					$key
				),
				"config_value" => array(
					"text",
					$value
				)
			), array(
				"config_key" => array(
					"text",
					$key
				)
			));
		}
	}


	/**
	 * @param $key
	 *
	 * @return bool|string
	 */
	public function getValue($key) {
		if (! isset(self::$cache[$key])) {
			global $ilDB;
			$result = $ilDB->query("SELECT config_value FROM " . $this->getTableName() . " WHERE config_key = " . $ilDB->quote($key, "text"));
			if ($result->numRows() == 0) {
				return false;
			}
			$record = $ilDB->fetchAssoc($result);
			self::$cache[$key] = (string)$record['config_value'];
		}

		return self::$cache[$key];
	}


	/**
	 * @return bool
	 */
	public function initDB() {
		global $ilDB;
		if (! $ilDB->tableExists($this->getTableName())) {
			$fields = array(
				'config_key' => array(
					'type' => 'text',
					'length' => 128,
					'notnull' => true
				),
				'config_value' => array(
					'type' => 'clob',
					'notnull' => false
				),
			);
			$ilDB->createTable($this->getTableName(), $fields);
			$ilDB->addPrimaryKey($this->getTableName(), array( "config_key" ));
		}

		return true;
	}


	//
	// Helper
	//
	/**
	 * @param string $str
	 *
	 * @return string
	 */
	public static function _fromCamelCase($str) {
		$str[0] = strtolower($str[0]);
		$func = create_function('$c', 'return "_" . strtolower($c[1]);');

		return preg_replace_callback('/([A-Z])/', $func, $str);
	}


	/**
	 * @param string $str
	 * @param bool   $capitalise_first_char
	 *
	 * @return string
	 */
	public static function _toCamelCase($str, $capitalise_first_char = false) {
		if ($capitalise_first_char) {
			$str[0] = strtoupper($str[0]);
		}
		$func = create_function('$c', 'return strtoupper($c[1]);');

		return preg_replace_callback('/-([a-z])/', $func, $str);
	}
}

?>
