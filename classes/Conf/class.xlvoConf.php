<?php

namespace LiveVoting\Conf;

use LiveVoting\Cache\CachingActiveRecord;

/**
 * Class xlvoConf
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoConf extends CachingActiveRecord {

	const CONFIG_VERSION = 2;
	const F_CONFIG_VERSION = 'config_version';
	const F_ALLOW_FREEZE = 'allow_freeze';
	const F_ALLOW_FULLSCREEN = 'allow_fullscreen';
	const F_ALLOW_SHORTLINK = 'allow_shortlink';
	const F_ALLOW_SHORTLINK_LINK = 'allow_shortlink_link';
	const F_BASE_URL = 'base_url';
	const F_ALLOW_GLOBAL_ANONYMOUS = 'global_anonymous';
	const F_REGENERATE_TOKEN = 'regenerate_token';
	const F_USE_QR = 'use_qr';
	const REWRITE_RULE = "RewriteRule ^vote(/[\\w]*|) Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php?pin=$1 [L]";
	const API_URL = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/ilias.php';
	const RESULT_API_URL = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/api.php';
	const F_REQUEST_FREQUENCY = 'request_frequency';
	const F_RESULT_API = 'result_api';
	const F_API_TYPE = 'api_type';
	const F_API_TOKEN = 'api_token';
	const F_USE_GLOBAL_CACHE = 'use_global_cache';
	/**
	 * Min client update frequency in seconds.
	 * This value should never be set bellow 1 second.
	 */
	const MIN_CLIENT_UPDATE_FREQUENCY = 1;
	/**
	 * Max client update frequency in seconds.
	 */
	const MAX_CLIENT_UPDATE_FREQUENCY = 60;
	const TABLE_NAME = 'xlvo_config';


	/**
	 * @return string
	 */
	public static function getShortLinkURL() {

		$url = null;
		$shortLinkEnabled = intval(self::getConfig(self::F_ALLOW_SHORTLINK));

		if ($shortLinkEnabled === 1) {
			$url = self::getConfig(self::F_ALLOW_SHORTLINK_LINK);
			$url = rtrim($url, "/") . "/";
		}
		else {
			$url = ILIAS_HTTP_PATH
			       . '/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php?pin=';
		}

		return $url;
	}


	/**
	 * @return bool
	 */
	public static function isLatexEnabled() {
		include_once "./Services/Administration/classes/class.ilSetting.php";
		$mathJaxSetting = new \ilSetting("MathJax");

		return (bool)$mathJaxSetting->get("enable");
	}


	/**
	 * @return string
	 */
	public static function getApiToken() {
		$token = self::getConfig(self::F_API_TOKEN);
		if (!$token) {
			$token = md5(time());
			self::set(self::F_API_TOKEN, $token);
		}

		return $token;
	}


	/**
	 * @return string
	 */
	public static function getBaseURL() {
		if (self::getConfig(self::F_ALLOW_SHORTLINK)) {
			$url = self::getConfig(self::F_BASE_URL);
			$url = rtrim($url, "/") . "/";
		} else {
			$str = strstr(ILIAS_HTTP_PATH, 'Customizing', true);
			$url = rtrim($str, "/") . "/";
		}

		return $url;
	}


	/**
	 * @return string
	 */
	public static function getFullApiURL() {
		return self::getBaseURL() . ltrim(self::API_URL, "./");
	}


	/**
	 * @return string
	 * @description Return the Name of your Database Table
	 * @deprecated
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @var array
	 */
	protected static $cache = array();
	/**
	 * @var array
	 */
	protected static $cache_loaded = array();
	/**
	 * @var bool
	 */
	protected $ar_safe_read = false;


	/**
	 * @return bool
	 */
	public static function isConfigUpToDate() {
		return self::getConfig(self::F_CONFIG_VERSION) == self::CONFIG_VERSION;
	}


	public static function load() {
		parent::get();
	}


	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public static function getConfig($name) {
		if (!self::$cache_loaded[$name]) {
			$obj = new self($name);
			self::$cache[$name] = json_decode($obj->getValue());
			self::$cache_loaded[$name] = true;
		}

		return self::$cache[$name];
	}


	/**
	 * @param $name
	 * @param $value
	 */
	public static function set($name, $value) {
		$obj = new self($name);
		$obj->setValue(json_encode($value));

		if (self::where(array( 'name' => $name ))->hasSets()) {
			$obj->update();
		} else {
			$obj->create();
		}
	}


	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_is_unique        true
	 * @db_is_primary       true
	 * @db_is_notnull       true
	 * @db_fieldtype        text
	 * @db_length           250
	 */
	protected $name;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           4000
	 */
	protected $value;


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}


	/**
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}
}