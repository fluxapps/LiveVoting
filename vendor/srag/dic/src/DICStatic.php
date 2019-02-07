<?php

namespace srag\DIC\LiveVoting;

use ilLogLevel;
use ilPlugin;
use srag\DIC\LiveVoting\DIC\DICInterface;
use srag\DIC\LiveVoting\DIC\Implementation\ILIAS52DIC;
use srag\DIC\LiveVoting\DIC\Implementation\ILIAS53DIC;
use srag\DIC\LiveVoting\DIC\Implementation\ILIAS54DIC;
use srag\DIC\LiveVoting\DIC\Implementation\LegacyDIC;
use srag\DIC\LiveVoting\Exception\DICException;
use srag\DIC\LiveVoting\Output\Output;
use srag\DIC\LiveVoting\Output\OutputInterface;
use srag\DIC\LiveVoting\Plugin\Plugin;
use srag\DIC\LiveVoting\Plugin\PluginInterface;
use srag\DIC\LiveVoting\Version\Version;
use srag\DIC\LiveVoting\Version\VersionInterface;

/**
 * Class DICStatic
 *
 * @package srag\DIC\LiveVoting
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class DICStatic implements DICStaticInterface {

	/**
	 * @var DICInterface|null
	 */
	private static $dic = NULL;
	/**
	 * @var OutputInterface|null
	 */
	private static $output = NULL;
	/**
	 * @var PluginInterface[]
	 */
	private static $plugins = [];
	/**
	 * @var VersionInterface|null
	 */
	private static $version = NULL;


	/**
	 * @inheritdoc
	 */
	public static function clearCache()/*: void*/ {
		self::$dic = NULL;
		self::$output = NULL;
		self::$plugins = [];
		self::$version = NULL;
	}


	/**
	 * @inheritdoc
	 */
	public static function dic()/*: DICInterface*/ {
		if (self::$dic === NULL) {
			switch (true) {
				case (self::version()->isLower(VersionInterface::ILIAS_VERSION_5_2)):
					global $GLOBALS;
					self::$dic = new LegacyDIC($GLOBALS);
					break;

				case (self::version()->isLower(VersionInterface::ILIAS_VERSION_5_3)):
					global $DIC;
					self::$dic = new ILIAS52DIC($DIC);
					break;

				case (self::version()->isLower(VersionInterface::ILIAS_VERSION_5_4)):
					global $DIC;
					self::$dic = new ILIAS53DIC($DIC);
					break;

				default:
					global $DIC;
					self::$dic = new ILIAS54DIC($DIC);
					break;
			}
		}

		return self::$dic;
	}


	/**
	 * @inheritdoc
	 */
	public static function output()/*: OutputInterface*/ {
		if (self::$output === NULL) {
			self::$output = new Output();
		}

		return self::$output;
	}


	/**
	 * @inheritdoc
	 */
	public static function plugin(/*string*/
		$plugin_class_name)/*: PluginInterface*/ {
		if (!isset(self::$plugins[$plugin_class_name])) {
			if (!class_exists($plugin_class_name)) {
				throw new DICException("Class $plugin_class_name not exists!", DICException::CODE_INVALID_PLUGIN_CLASS);
			}

			if (method_exists($plugin_class_name, "getInstance")) {
				$plugin_object = $plugin_class_name::getInstance();
			} else {
				$plugin_object = new $plugin_class_name();

				self::dic()->log()->write("DICLog: Please implement $plugin_class_name::getInstance()!", ilLogLevel::DEBUG);
			}

			if (!$plugin_object instanceof ilPlugin) {
				throw new DICException("Class $plugin_class_name not extends ilPlugin!", DICException::CODE_INVALID_PLUGIN_CLASS);
			}

			self::$plugins[$plugin_class_name] = new Plugin($plugin_object);
		}

		return self::$plugins[$plugin_class_name];
	}


	/**
	 * @inheritdoc
	 */
	public static function version()/*: VersionInterface*/ {
		if (self::$version === NULL) {
			self::$version = new Version();
		}

		return self::$version;
	}


	/**
	 * DICStatic constructor
	 */
	private function __construct() {

	}
}
