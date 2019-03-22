<?php

namespace srag\RemovePluginDataConfirm\LiveVoting;

use ilAdministrationGUI;
use ilConfirmationGUI;
use ilObjComponentSettingsGUI;
use ilSession;
use ilUtil;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class AbstractRemovePluginDataConfirm
 *
 * @package srag\RemovePluginDataConfirm\LiveVoting
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractRemovePluginDataConfirm {

	use DICTrait;
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_CANCEL = "cancel";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_CONFIRM_REMOVE_DATA = "confirmRemoveData";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_DEACTIVATE = "deactivate";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_SET_KEEP_DATA = "setKeepData";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const CMD_SET_REMOVE_DATA = "setRemoveData";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const KEY_UNINSTALL_REMOVES_DATA = "uninstall_removes_data";
	/**
	 * @var string
	 *
	 * @internal
	 */
	const LANG_MODULE = "removeplugindataconfirm";
	/**
	 * @var AbstractRemovePluginDataConfirm|null
	 *
	 * @internal
	 */
	private static $instance = NULL;


	/**
	 * @return AbstractRemovePluginDataConfirm
	 *
	 * @internal
	 */
	public static final function getInstance()/*: AbstractRemovePluginDataConfirm*/ {
		if (self::$instance === NULL) {
			self::$instance = new static();
		}

		return self::$instance;
	}


	/**
	 * @param bool $plugin
	 *
	 * @internal
	 */
	public static final function saveParameterByClass(/*bool*/
		$plugin = true)/*: void*/ {
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
	 * @internal
	 */
	public final function __construct() {

	}


	/**
	 * @internal
	 */
	public final function executeCommand()/*: void*/ {
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
	 *
	 * @internal
	 */
	private final function redirectToPlugins(/*string*/
		$cmd)/*: void*/ {
		self::saveParameterByClass($cmd !== "listPlugins");

		self::dic()->ctrl()->redirectByClass([
			ilAdministrationGUI::class,
			ilObjComponentSettingsGUI::class
		], $cmd);
	}


	/**
	 * @internal
	 */
	private final function cancel()/*: void*/ {
		$this->redirectToPlugins("listPlugins");
	}


	/**
	 * @internal
	 */
	private final function confirmRemoveData()/*: void*/ {
		self::saveParameterByClass();

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText($this->txt("confirm_remove_data"));

		$confirmation->addItem("_", "_", $this->txt("data"));

		$confirmation->addButton($this->txt("remove_data"), self::CMD_SET_REMOVE_DATA);
		$confirmation->addButton($this->txt("keep_data"), self::CMD_SET_KEEP_DATA);
		$confirmation->addButton($this->txt("deactivate"), self::CMD_DEACTIVATE);
		$confirmation->setCancel($this->txt("cancel"), self::CMD_CANCEL);

		self::output()->output($confirmation, true);
	}


	/**
	 * @internal
	 */
	private final function deactivate()/*: void*/ {
		$this->redirectToPlugins("deactivatePlugin");
	}


	/**
	 * @internal
	 */
	private final function setKeepData()/*: void*/ {
		$this->setUninstallRemovesData(false);

		ilUtil::sendInfo($this->txt("msg_kept_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}


	/**
	 * @internal
	 */
	private final function setRemoveData()/*: void*/ {
		$this->setUninstallRemovesData(true);

		ilUtil::sendInfo($this->txt("msg_removed_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}


	/**
	 * @param string $key
	 *
	 * @return string
	 *
	 * @internal
	 */
	private final function txt(/*string*/
		$key)/*: string*/ {
		return self::plugin()->translate($key, self::LANG_MODULE, [ self::plugin()->getPluginObject()->getPluginName() ]);
	}


	/**
	 * @return bool|null
	 *
	 * @internal
	 */
	public final function getUninstallRemovesData()/*: ?bool*/ {
		return json_decode(ilSession::get(self::KEY_UNINSTALL_REMOVES_DATA));
	}


	/**
	 * @param bool $uninstall_removes_data
	 *
	 * @internal
	 */
	public final function setUninstallRemovesData(/*bool*/
		$uninstall_removes_data)/*: void*/ {
		ilSession::set(self::KEY_UNINSTALL_REMOVES_DATA, json_encode($uninstall_removes_data));
	}


	/**
	 * @internal
	 */
	public final function removeUninstallRemovesData()/*: void*/ {
		ilSession::clear(self::KEY_UNINSTALL_REMOVES_DATA);
	}
}
