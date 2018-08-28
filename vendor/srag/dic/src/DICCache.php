<?php

namespace srag\DIC;

use ilLanguage;
use ilPlugin;

/**
 * Class DICCache
 *
 * @package srag\DIC
 */
final class DICCache {

	/**
	 * @var DICInterface|null
	 */
	private static $dic = NULL;
	/**
	 * @var ilLanguage[]
	 */
	private static $languages = [];
	/**
	 * @var ilPlugin[]
	 */
	private static $pl = [];


	/**
	 * @return DICInterface
	 */
	public static function dic() {
		if (self::$dic === NULL) {
			if (ILIAS_VERSION_NUMERIC >= "5.2") {
				global $DIC;
				self::$dic = new NewDIC($DIC);
			} else {
				global $GLOBALS;
				self::$dic = new LegacyDIC($GLOBALS);
			}
		}

		return self::$dic;
	}


	/**
	 * @param string $lang
	 *
	 * @return ilLanguage
	 */
	public static function language($lang) {
		if (!isset(self::$languages[$lang])) {
			self::$languages[$lang] = new ilLanguage($lang);
		}

		return self::$languages[$lang];
	}


	/**
	 * @param string $plugin_class_name
	 *
	 * @return ilPlugin
	 * @throws DICException Class $plugin_class_name not exists!
	 */
	public static function pl($plugin_class_name) {
		if (!isset(self::$pl[$plugin_class_name])) {
			if (!class_exists($plugin_class_name)) {
				throw new DICException("Class $plugin_class_name not exists!");
			}

			if (method_exists($plugin_class_name, "getInstance")) {
				self::$pl[$plugin_class_name] = $plugin_class_name::getInstance();
			} else {
				// TODO: Ev. some log in ILIAS log to implement getInstance
				self::$pl[$plugin_class_name] = new $plugin_class_name();
			}
		}

		return self::$pl[$plugin_class_name];
	}


	/**
	 * DICCache constructor
	 */
	private function __construct() {

	}
}
