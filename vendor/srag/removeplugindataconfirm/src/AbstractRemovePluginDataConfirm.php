<?php

namespace srag\RemovePluginDataConfirm;

use ilAdministrationGUI;
use ilConfirmationGUI;
use ilObjComponentSettingsGUI;
use ilUtil;
use srag\DIC\DICTrait;

/**
 * Class AbstractRemovePluginDataConfirm
 *
 * @package srag\RemovePluginDataConfirm
 */
abstract class AbstractRemovePluginDataConfirm {

	use DICTrait;
	const CMD_CANCEL = "cancel";
	const CMD_CONFIRM_REMOVE_DATA = "confirmRemoveData";
	const CMD_DEACTIVATE = "deactivate";
	const CMD_SET_KEEP_DATA = "setKeepData";
	const CMD_SET_REMOVE_DATA = "setRemoveData";
	const KEY_UNINSTALL_REMOVES_DATA = "uninstall_removes_data";
	const DEFAULT_UNINSTALL_REMOVES_DATA = NULL;
	/**
	 * @var static|null
	 */
	protected static $instance = NULL;


	/**
	 * @return static
	 */
	public static final function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new static();
		}

		return self::$instance;
	}


	/**
	 * @param bool $plugin
	 */
	public static final function saveParameterByClass($plugin = true) {
		$ref_id = filter_input(INPUT_GET, "ref_id");
		self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ref_id", $ref_id);
		self::dic()->ctrl()->setParameterByClass(static::class, "ref_id", $ref_id);

		if ($plugin) {
			$ctype = filter_input(INPUT_GET, "ctype");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ctype", $ctype);
			self::dic()->ctrl()->setParameterByClass(static::class, "ctype", $ctype);

			$cname = filter_input(INPUT_GET, "cname");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "cname", $cname);
			self::dic()->ctrl()->setParameterByClass(static::class, "cname", $cname);

			$slot_id = filter_input(INPUT_GET, "slot_id");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "slot_id", $slot_id);
			self::dic()->ctrl()->setParameterByClass(static::class, "slot_id", $slot_id);

			$plugin_id = filter_input(INPUT_GET, "plugin_id");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "plugin_id", $plugin_id);
			self::dic()->ctrl()->setParameterByClass(static::class, "plugin_id", $plugin_id);

			$pname = filter_input(INPUT_GET, "pname");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "pname", $pname);
			self::dic()->ctrl()->setParameterByClass(static::class, "pname", $pname);
		}
	}


	/**
	 *
	 */
	public final function __construct() {

	}


	/**
	 *
	 */
	public final function executeCommand() {
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch ($next_class) {
			default:
				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_CANCEL:
					case self::CMD_CONFIRM_REMOVE_DATA:
					case self::CMD_DEACTIVATE:
					case self::CMD_SET_KEEP_DATA:
					case self::CMD_SET_REMOVE_DATA:
						$this->{$cmd}();
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 * @param string $cmd
	 */
	protected final function redirectToPlugins($cmd) {
		self::saveParameterByClass($cmd !== "listPlugins");

		self::dic()->ctrl()->redirectByClass([
			ilAdministrationGUI::class,
			ilObjComponentSettingsGUI::class
		], $cmd);
	}


	/**
	 *
	 */
	protected final function cancel() {
		$this->redirectToPlugins("listPlugins");
	}


	/**
	 *
	 */
	protected final function confirmRemoveData() {
		self::saveParameterByClass();

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText($this->txt("confirm_remove_data"));

		$confirmation->addItem("_", "_", $this->txt("data"));

		$confirmation->addButton($this->txt("remove_data"), self::CMD_SET_REMOVE_DATA);
		$confirmation->addButton($this->txt("keep_data"), self::CMD_SET_KEEP_DATA);
		$confirmation->addButton($this->txt("deactivate"), self::CMD_DEACTIVATE);
		$confirmation->setCancel($this->txt("cancel"), self::CMD_CANCEL);

		self::plugin()->output($confirmation);
	}


	/**
	 *
	 */
	protected final function deactivate() {
		$this->redirectToPlugins("deactivatePlugin");
	}


	/**
	 *
	 */
	protected final function setKeepData() {
		$this->setUninstallRemovesData(false);

		ilUtil::sendInfo($this->txt("msg_kept_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}


	/**
	 *
	 */
	protected final function setRemoveData() {
		$this->setUninstallRemovesData(true);

		ilUtil::sendInfo($this->txt("msg_removed_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}


	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected final function txt($key) {
		return self::plugin()->translate($key, "removeplugindataconfirm", [ self::plugin()->getPluginObject()->getPluginName() ]);
	}


	/**
	 * Return from your config database, if the plugin data should be removed on uninstall (bool) or should be confirmed if not exists (null)
	 *
	 * @return bool|null
	 */
	public abstract function getUninstallRemovesData();


	/**
	 * Reset in your config database, that the plugin data should be removed on uninstall. `getUninstallRemovesData` should now return `null`
	 */
	public abstract function removeUninstallRemovesData();


	/**
	 * Set in your config database, that the plugin data should be removed or not on uninstall
	 *
	 * @param bool $uninstall_removes_data
	 */
	public abstract function setUninstallRemovesData($uninstall_removes_data);
}
