<?php

namespace srag\ActiveRecordConfig\LiveVoting;

use ilPluginConfigGUI;
use ilUtil;
use srag\ActiveRecordConfig\LiveVoting\Exception\ActiveRecordConfigException;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class ActiveRecordConfigGUI
 *
 * @package srag\ActiveRecordConfig\LiveVoting
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ActiveRecordConfigGUI extends ilPluginConfigGUI {

	use DICTrait;
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_APPLY_FILTER = "applyFilter";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_RESET_FILTER = "resetFilter";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_CONFIGURE = "configure";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_UPDATE_CONFIGURE = "updateConfigure";
	/**
	 * @var string
	 */
	const LANG_MODULE_CONFIG = "config";
	/**
	 * @var string
	 */
	const TAB_CONFIGURATION = "configuration";
	/**
	 * @var array
	 *
	 * @abstract
	 */
	protected static $tabs = [ self::TAB_CONFIGURATION => ActiveRecordConfigFormGUI::class ];
	/**
	 * @var array
	 */
	protected static $custom_commands = [];


	/**
	 * ActiveRecordConfigGUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @internal
	 */
	public final function executeCommand() {
		parent::executeCommand();
	}


	/**
	 * @param string $cmd
	 *
	 * @throws ActiveRecordConfigException Unknown command $cmd!
	 * @throws ActiveRecordConfigException Class $config_gui_class_name not extends ActiveRecordConfigFormGUI, ActiveRecordObjectFormGUI or ActiveRecordConfigTableGUI!
	 *
	 * @internal
	 */
	public final function performCommand(/*string*/
		$cmd)/*: void*/ {
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				$this->setTabs();

				switch (true) {
					case (in_array($cmd, static::$custom_commands)):
						$this->{$cmd}();
						break;

					case ($cmd === self::CMD_CONFIGURE):
						reset(static::$tabs);
						$this->configure(key(static::$tabs));
						break;

					case (strpos($cmd, $this->getCmdForTab("")) === 0):
						$tab_id = substr($cmd, strlen($this->getCmdForTab("")));

						$this->configure($tab_id);
						break;

					case (strpos($cmd, self::CMD_UPDATE_CONFIGURE . "_") === 0):
						$tab_id = substr($cmd, strlen(self::CMD_UPDATE_CONFIGURE . "_"));

						$this->updateConfigure($tab_id);
						break;

					case (strpos($cmd, self::CMD_APPLY_FILTER . "_") === 0):
						$tab_id = substr($cmd, strlen(self::CMD_APPLY_FILTER . "_"));

						$this->applyFilter($tab_id);
						break;

					case (strpos($cmd, self::CMD_RESET_FILTER . "_") === 0):
						$tab_id = substr($cmd, strlen(self::CMD_RESET_FILTER . "_"));

						$this->resetFilter($tab_id);
						break;

					default:
						throw new ActiveRecordConfigException("Unknown command $cmd!", ActiveRecordConfigException::CODE_UNKOWN_COMMAND);
						break;
				}
				break;
		}
	}


	/**
	 * @internal
	 */
	private final function setTabs() {
		foreach (static::$tabs as $tab_id => $config_gui_class_name) {
			self::dic()->tabs()->addTab($tab_id, $this->txt($tab_id), self::dic()->ctrl()->getLinkTarget($this, $this->getCmdForTab($tab_id)));
		}
	}


	/**
	 * @param string $tab_id
	 *
	 * @return string
	 */
	public final function getCmdForTab(/*string*/
		$tab_id)/*: void*/ {
		return self::CMD_CONFIGURE . "_" . $tab_id;
	}


	/**
	 * @param string $tab_id
	 */
	public final function redirectToTab(/*string*/
		$tab_id)/*: void*/ {
		self::dic()->ctrl()->redirect($this, $this->getCmdForTab($tab_id));
	}


	/**
	 * @param string $tab_id
	 *
	 * @throws ActiveRecordConfigException Class $config_gui_class_name not extends ActiveRecordConfigFormGUI, ActiveRecordObjectFormGUI or ActiveRecordConfigTableGUI!
	 *
	 * @internal
	 */
	private final function configure(/*string*/
		$tab_id)/*: void*/ {
		self::dic()->tabs()->activateTab($tab_id);

		$gui = $this->getConfigurationGUI($tab_id);

		self::output()->output($gui);
	}


	/**
	 * @param string $tab_id
	 *
	 * @throws ActiveRecordConfigException Class $config_gui_class_name not extends ActiveRecordConfigFormGUI or ActiveRecordObjectFormGUI!
	 *
	 * @internal
	 */
	private final function updateConfigure(/*string*/
		$tab_id)/*: void*/ {
		self::dic()->tabs()->activateTab($tab_id);

		$form = $this->getConfigurationFormGUI(static::$tabs[$tab_id], $tab_id);

		if (!$form->storeForm()) {
			self::output()->output($form);

			return;
		}

		ilUtil::sendSuccess($this->txt($tab_id . "_saved"), true);

		$this->redirectToTab($tab_id);
	}


	/**
	 * @param string $tab_id
	 *
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigTableGUI!
	 *
	 * @internal
	 */
	private final function applyFilter(/*string*/
		$tab_id)/*: void*/ {
		$table = $this->getConfigurationTable(static::$tabs[$tab_id], self::CMD_APPLY_FILTER . "_" . $tab_id, $tab_id);

		$table->writeFilterToSession();

		$this->redirectToTab($tab_id);
	}


	/**
	 * @param string $tab_id
	 *
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigTableGUI!
	 *
	 * @internal
	 */
	private final function resetFilter(/*string*/
		$tab_id)/*: void*/ {
		$table = $this->getConfigurationTable(static::$tabs[$tab_id], self::CMD_RESET_FILTER . "_" . $tab_id, $tab_id);

		$table->resetFilter();

		$table->resetOffset();

		$this->redirectToTab($tab_id);
	}


	/**
	 * @param string $tab_id
	 *
	 * @return ActiveRecordConfigFormGUI|ActiveRecordObjectFormGUI|ActiveRecordConfigTableGUI
	 *
	 * @throws ActiveRecordConfigException Class $config_gui_class_name not extends ActiveRecordConfigFormGUI, ActiveRecordObjectFormGUI or ActiveRecordConfigTableGUI!
	 *
	 * @internal
	 */
	private final function getConfigurationGUI(/*string*/
		$tab_id) {
		$config_gui_class_name = static::$tabs[$tab_id];

		switch (true) {
			case (substr($config_gui_class_name, - strlen("FormGUI")) === "FormGUI"):
				$config_gui = $this->getConfigurationFormGUI($config_gui_class_name, $tab_id);
				break;

			case (substr($config_gui_class_name, - strlen("TableGUI")) === "TableGUI"):
				$config_gui = $this->getConfigurationTable($config_gui_class_name, $this->getCmdForTab($tab_id), $tab_id);
				break;

			default:
				throw new ActiveRecordConfigException("Class $config_gui_class_name not extends ActiveRecordConfigFormGUI, ActiveRecordObjectFormGUI or ActiveRecordConfigTableGUI!", ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
				break;
		}

		return $config_gui;
	}


	/**
	 * @param string $config_form_gui_class_name
	 * @param string $tab_id
	 *
	 * @return ActiveRecordConfigFormGUI|ActiveRecordObjectFormGUI
	 *
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not exists!
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigFormGUI or ActiveRecordObjectFormGUI!
	 *
	 * @internal
	 */
	private final function getConfigurationFormGUI(/*string*/
		$config_form_gui_class_name, /*string*/
		$tab_id)/*: ActiveRecordConfigFormGUI*/ {
		if (!class_exists($config_form_gui_class_name)) {
			throw new ActiveRecordConfigException("Class $config_form_gui_class_name not exists!", ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
		}

		$config_form_gui = new $config_form_gui_class_name($this, $tab_id);

		if (!($config_form_gui instanceof ActiveRecordConfigFormGUI || $config_form_gui instanceof ActiveRecordObjectFormGUI)) {
			throw new ActiveRecordConfigException("Class $config_form_gui_class_name not extends ActiveRecordConfigFormGUI or ActiveRecordObjectFormGUI!", ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
		}

		return $config_form_gui;
	}


	/**
	 * @param string $config_table_gui_class_name
	 * @param string $parent_cmd
	 * @param string $tab_id
	 *
	 * @return ActiveRecordConfigTableGUI
	 *
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not exists!
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigTableGUI!
	 *
	 * @internal
	 */
	private final function getConfigurationTable(/*string*/
		$config_table_gui_class_name,/*string*/
		$parent_cmd, /*string*/
		$tab_id)/*: ActiveRecordConfigTableGUI*/ {
		if (!class_exists($config_table_gui_class_name)) {
			throw new ActiveRecordConfigException("Class $config_table_gui_class_name not exists!", ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
		}

		$config_table_gui = new $config_table_gui_class_name($this, $parent_cmd, $tab_id);

		if (!$config_table_gui instanceof ActiveRecordConfigTableGUI) {
			throw new ActiveRecordConfigException("Class $config_table_gui_class_name not extends ActiveRecordConfigTableGUI!", ActiveRecordConfigException::CODE_INVALID_CONFIG_GUI_CLASS);
		}

		return $config_table_gui;
	}


	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected final function txt(/*string*/
		$key)/*: string*/ {
		return self::plugin()->translate($key, self::LANG_MODULE_CONFIG);
	}
}
