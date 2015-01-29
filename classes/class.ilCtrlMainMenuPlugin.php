<?php
require_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmm.php');

/**
 * Class ilCtrlMainMenuPlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Michael Herren <mh@studer-raimann.ch>
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


	protected function init() {
		$this->checkAR44();
		self::loadActiveRecord();
	}


	/**
	 * @return ilCtrlMainMenuPlugin
	 */
	public static function getInstance() {
		if (!isset(self::$plugin_cache)) {
			self::$plugin_cache = new ilCtrlMainMenuPlugin();
		}

		return self::$plugin_cache;
	}


	/**
	 * @param      $a_template
	 * @param bool $a_par1
	 * @param bool $a_par2
	 *
	 * @return ilTemplate
	 */
	public function getVersionTemplate($a_template, $a_par1 = true, $a_par2 = true) {
		if (ctrlmm::is50()) {
			$a_template = 'ilias5/' . $a_template;
		}

		return $this->getTemplate($a_template, $a_par1, $a_par2);
	}


	public static function loadActiveRecord() {
		if (ctrlmm::is50()) {
			require_once('./Services/ActiveRecord/class.ActiveRecord.php');
		} else {
			require_once('./Customizing/global/plugins/Libraries/ActiveRecord/class.ActiveRecord.php');
		}
	}


	/**
	 * @return bool
	 * @throws ilPluginException
	 */
	protected function beforeActivation() {
		$this->checkAR44();

		return true;
	}


	/**
	 * @throws ilPluginException
	 */
	protected function checkAR44() {
		if (ctrlmm::is44() OR ctrlmm::is43()) {
			if (!is_file('./Customizing/global/plugins/Libraries/ActiveRecord/class.ActiveRecord.php')) {
				throw new ilPluginException('Please install ActiveRecord First');
			}
		}
	}
}

?>
