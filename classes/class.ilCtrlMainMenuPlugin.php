<?php

require_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');
require_once('class.ilCtrlMainMenuConfig.php');

/**
 * @author  Alex Killing <alex.killing@gmx.de>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ilCtrlMainMenuPlugin extends ilUserInterfaceHookPlugin {

	const CONFIG_TABLE = 'uihkctrlmainmenu_c';
	/**
	 * @var ilCtrlMainMenuConfig
	 */
	protected static $config_cache;
	/**
	 * @var ilCtrlMainMenuPlugin
	 */
	protected static $plugin_cache;


	/**
	 * @return string
	 */
	public function getPluginName() {
		return 'CtrlMainMenu';
	}


	/**
	 * @return ilCtrlMainMenuPlugin
	 */
	public static function get() {
		if (! isset(self::$plugin_cache)) {
			self::$plugin_cache = new ilCtrlMainMenuPlugin();
		}

		return self::$plugin_cache;
	}


	/**
	 * @return ilCtrlMainMenuConfig
	 * @deprecated
	 */
	public function getConfigObject() {
		if (! isset(self::$config_cache)) {
			self::$config_cache = new ilCtrlMainMenuConfig(self::CONFIG_TABLE);
		}

		return self::$config_cache;
	}


	/**
	 * @return ilCtrlMainMenuConfig
	 * @deprecated Use ilCtrlMainMenuConfig::getInstance() instead
	 */
	public static function getConf() {
		return ilCtrlMainMenuConfig::getInstance();
	}


	/**
	 * @return ilCtrlMainMenuConfig
	 * @deprecated
	 */
	public function conf() {
		return self::getConf();
	}
}

?>
