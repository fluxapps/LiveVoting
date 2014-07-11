<?php


/**
 * ilCtrlMainMenuConfig
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 */
class ilCtrlMainMenuConfig {

	const TABLE_NAME = ilCtrlMainMenuPlugin::CONFIG_TABLE;
	const ILIAS_43 = 43;
	const ILIAS_44 = 44;
	const ILIAS_45 = 45;
	/**
	 * @var string
	 */
	protected $table_name = '';
	/**
	 * @var array
	 */
	protected static $cache = array();
	/**
	 * @var ilCtrlMainMenuConfig
	 */
	protected static $instance;


	public static function getInstance() {
		if (! isset(self::$instance)) {
			self::$instance = new self(self::TABLE_NAME);
		}

		return self::$instance;
	}


	/**
	 * @param $key
	 *
	 * @return bool|string
	 */
	public static function get($key) {
		return self::getInstance()->getValue($key);
	}


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
	 * @deprecated
	 *
	 * @return bool|null
	 */
	public function __call($method, $params) {
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
	 * @return int
	 */
	public static function getILIASVersion() {
		if (ilComponent::isVersionGreaterString(ILIAS_VERSION_NUMERIC, '4.5.000')) {
			return self::ILIAS_45;
		}
		if (ilComponent::isVersionGreaterString(ILIAS_VERSION_NUMERIC, '4.4.000')) {
			return self::ILIAS_44;
		}
		if (ilComponent::isVersionGreaterString(ILIAS_VERSION_NUMERIC, '4.3.000')) {
			return self::ILIAS_43;
		}

		return 0;
	}


	/**
	 * @return bool
	 */
	public static function is44() {
		return self::getILIASVersion() >= self::ILIAS_44;
	}


	/**
	 * @return bool
	 */
	public static function is43() {
		return self::getILIASVersion() >= self::ILIAS_43;
	}


	/**
	 * @return bool
	 */
	public static function is45() {
		return self::getILIASVersion() >= self::ILIAS_45;
	}


	/**
	 * @param $key
	 * @param $value
	 *
	 * @return bool
	 */
	public function setValue($key, $value) {
		global $ilDB;
		if (! (bool)$this->hasEntry($key)) {
			$ilDB->insert($this->getTableName(), array(
				'config_key' => array(
					'text',
					$key
				),
				'config_value' => array(
					'text',
					$value
				)
			));
		} else {
			$ilDB->update($this->getTableName(), array(
				'config_key' => array(
					'text',
					$key
				),
				'config_value' => array(
					'text',
					$value
				)
			), array(
				'config_key' => array(
					'text',
					$key
				)
			));
		}
		self::$cache[$key] = $value;
	}


	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	protected function getResult($key) {
		global $ilDB;
		$result = $ilDB->query('SELECT config_value FROM ' . $this->getTableName() . ' WHERE config_key = ' . $ilDB->quote($key, 'text'));

		return $result;
	}


	/**
	 * @param $key
	 *
	 * @return bool|string
	 */
	public function getValue($key) {
		if (! isset(self::$cache[$key])) {
			global $ilDB;
			$record = $ilDB->fetchObject($this->getResult($key));

			self::$cache[$key] = (string)$record->config_value;
		}

		return self::$cache[$key];
	}


	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function hasEntry($key) {
		global $ilDB;
		$result = $this->getResult($key);

		return ($ilDB->numRows($result) > 0);
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
			$ilDB->addPrimaryKey($this->getTableName(), array( 'config_key' ));
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
