<?php

namespace LiveVoting\Conf;

use ilSetting;
use LiveVoting\Cache\CachingActiveRecord;

/**
 * Class xlvoConf
 *
 * @package    LiveVoting\Conf
 * @author     Fabian Schmid <fs@studer-raimann.ch>
 *
 * @deprecated TODO: Use srag\ActiveRecordConfig\LiveVoting
 */
class xlvoConf extends CachingActiveRecord {

	/**
	 * @var int
	 *
	 * @deprecated
	 */
	const CONFIG_VERSION = 2;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_CONFIG_VERSION = 'config_version';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_ALLOW_FREEZE = 'allow_freeze';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_ALLOW_FULLSCREEN = 'allow_fullscreen';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_ALLOW_SHORTLINK_VOTE = 'allow_shortlink';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_ALLOW_SHORTLINK_VOTE_LINK = 'allow_shortlink_link';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_BASE_URL_VOTE = 'base_url';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_ALLOW_GLOBAL_ANONYMOUS = 'global_anonymous';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_REGENERATE_TOKEN = 'regenerate_token';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_USE_QR = 'use_qr';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const REWRITE_RULE_VOTE = "RewriteRule ^/?vote(/\\w*)? /Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php?xlvo_pin=$1 [L]";
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const API_URL = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/ilias.php';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const RESULT_API_URL = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/api.php';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_REQUEST_FREQUENCY = 'request_frequency';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_RESULT_API = 'result_api';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_USE_SERIF_FONT_FOR_PINS = 'use_serif_font_for_pins';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_API_TYPE = 'api_type';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_API_TOKEN = 'api_token';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_USE_GLOBAL_CACHE = 'use_global_cache';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_ACTIVATE_POWERPOINT_EXPORT = 'ppt_export';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_ALLOW_SHORTLINK_PRESENTER = 'allow_shortlink_presenter';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const F_ALLOW_SHORTLINK_PRESENTER_LINK = 'allow_shortlink_link_presenter';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const REWRITE_RULE_PRESENTER = "RewriteRule ^/?presenter(/\\w*)(/\\w*)(/\\w*)?(/\\w*)? /Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/presenter.php?xlvo_pin=$1&xlvo_puk=$2&xlvo_voting=$3&xlvo_ppt=$4 [L]";
	/**
	 * Min client update frequency in seconds.
	 * This value should never be set bellow 1 second.
	 *
	 * @var string
	 *
	 * @deprecated
	 */
	const MIN_CLIENT_UPDATE_FREQUENCY = 1;
	/**
	 * Max client update frequency in seconds.
	 *
	 * @var string
	 *
	 * @deprecated
	 */
	const MAX_CLIENT_UPDATE_FREQUENCY = 60;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const TABLE_NAME = 'xlvo_config';


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return bool
	 *
	 * @deprecated
	 */
	public static function isLatexEnabled() {
		$mathJaxSetting = new ilSetting("MathJax");

		return (bool)$mathJaxSetting->get("enable");
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function getApiToken() {
		$token = self::getConfig(self::F_API_TOKEN);
		if (!$token) {
			$token = md5(time()); // TODO: Use other not depcreated, safer hash algo (Like `hash("sha256", $hash)`)
			self::set(self::F_API_TOKEN, $token);
		}

		return $token;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function getBaseVoteURL() {
		if (self::getConfig(self::F_ALLOW_SHORTLINK_VOTE)) {
			$url = self::getConfig(self::F_BASE_URL_VOTE);
			$url = rtrim($url, "/") . "/";
		} else {
			$str = strstr(ILIAS_HTTP_PATH, 'Customizing', true);
			$url = rtrim($str, "/") . "/";
		}

		return $url;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function getFullApiURL() {
		return self::getBaseVoteURL() . ltrim(self::API_URL, "./");
	}


	/**
	 * @var array
	 *
	 * @deprecated
	 */
	protected static $cache = array();
	/**
	 * @var array
	 *
	 * @deprecated
	 */
	protected static $cache_loaded = array();
	/**
	 * @var bool
	 *
	 * @deprecated
	 */
	protected $ar_safe_read = false;


	/**
	 * @return bool
	 *
	 * @deprecated
	 */
	public static function isConfigUpToDate() {
		return self::getConfig(self::F_CONFIG_VERSION) == self::CONFIG_VERSION;
	}


	/**
	 * @deprecated
	 */
	public static function load() {
		parent::get();
	}


	/**
	 * @param $name
	 *
	 * @return mixed
	 *
	 * @deprecated
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
	 *
	 * @deprecated
	 */
	public static function set($name, $value) {
		$obj = new self($name);
		$obj->setValue(json_encode($value));

		$obj->store();
	}


	/**
	 * @param string $name
	 *
	 * @deprecated
	 */
	public static function remove($name) {
		/**
		 * @var xlvoConf $obj
		 */
		$obj = self::find($name);
		if ($obj !== NULL) {
			$obj->delete();
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
	 *
	 * @deprecated
	 */
	protected $name;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           4000
	 *
	 * @deprecated
	 */
	protected $value;


	/**
	 * @param string $name
	 *
	 * @deprecated
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $value
	 *
	 * @deprecated
	 */
	public function setValue($value) {
		$this->value = $value;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getValue() {
		return $this->value;
	}
}
