<?php

namespace srag\DIC;

use ilLogLevel;
use League\Flysystem\PluginInterface;
use srag\DIC\DIC\DICInterface;
use srag\DIC\DIC\LegacyDIC;
use srag\DIC\DIC\NewDIC;
use srag\DIC\Exception\DICException;
use srag\DIC\Plugin\Plugin;

/**
 * Class DICStatic
 *
 * @package srag\DIC
 */
final class DICStatic implements DICStaticInterface {

	/**
	 * @var DICInterface|null
	 */
	private static $dic = NULL;
	/**
	 * @var PluginInterface[]
	 */
	private static $plugins = [];


	/**
	 * @inheritdoc
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
	 * @inheritdoc
	 */
	public static function plugin($plugin_class_name) {
		if (!isset(self::$plugins[$plugin_class_name])) {
			if (!class_exists($plugin_class_name)) {
				throw new DICException("Class $plugin_class_name not exists!");
			}

			if (method_exists($plugin_class_name, "getInstance")) {
				$plugin = $plugin_class_name::getInstance();
			} else {
				$plugin = new $plugin_class_name();

				self::dic()->log()->write("DICLog: Please implement $plugin_class_name::getInstance()!", ilLogLevel::DEBUG);
			}

			self::$plugins[$plugin_class_name] = new Plugin($plugin);
		}

		return self::$plugins[$plugin_class_name];
	}


	/**
	 * DICStatic constructor
	 */
	private function __construct() {

	}
}
