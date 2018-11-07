<?php

namespace srag\RemovePluginDataConfirm;

use ilAdministrationGUI;
use ilConfirmationGUI;
use ilObjComponentSettingsGUI;
use ilSession;
use ilUtil;
use srag\DIC\DICTrait;

/**
 * Class AbstractRemovePluginDataConfirm
 *
 * @package srag\RemovePluginDataConfirm
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractRemovePluginDataConfirm {

	use DICTrait;
	const CMD_CANCEL = "cancel";
	const CMD_CONFIRM_REMOVE_DATA = "confirmRemoveData";
	const CMD_DEACTIVATE = "deactivate";
	const CMD_SET_KEEP_DATA = "setKeepData";
	const CMD_SET_REMOVE_DATA = "setRemoveData";
	/**
	 * @var string
	 *
	 * @access namespace
	 */
	const KEY_UNINSTALL_REMOVES_DATA = "uninstall_removes_data";
	/**
	 * @var string
	 *
	 * @access namespace
	 */
	const LANG_MODULE = "removeplugindataconfirm";
	/**
	 * @var AbstractRemovePluginDataConfirm|null
	 */
	private static $instance = NULL;


	/**
	 * @return AbstractRemovePluginDataConfirm
	 *
	 * @access namespace
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
	 * @access namespace
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
	 * @access namespace
	 */
	public final function __construct() {

	}


	/**
	 * @access namespace
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
	 *
	 */
	private final function cancel()/*: void*/ {
		$this->redirectToPlugins("listPlugins");
	}


	/**
	 *
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

		self::plugin()->output($confirmation);
	}


	/**
	 *
	 */
	private final function deactivate()/*: void*/ {
		$this->redirectToPlugins("deactivatePlugin");
	}


	/**
	 *
	 */
	private final function setKeepData()/*: void*/ {
		$this->setUninstallRemovesData(false);

		ilUtil::sendInfo($this->txt("msg_kept_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}


	/**
	 *
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
	 */
	private final function txt(/*string*/
		$key)/*: string*/ {
		return self::plugin()->translate($key, self::LANG_MODULE, [ self::plugin()->getPluginObject()->getPluginName() ]);
	}


	/**
	 * @return bool|null
	 */
	public final function getUninstallRemovesData()/*: ?bool*/ {
		return json_decode(ilSession::get(self::KEY_UNINSTALL_REMOVES_DATA));
	}


	/**
	 * @param bool $uninstall_removes_data
	 */
	public final function setUninstallRemovesData(/*bool*/
		$uninstall_removes_data)/*: void*/ {
		ilSession::set(self::KEY_UNINSTALL_REMOVES_DATA, json_encode($uninstall_removes_data));
	}


	/**
	 *
	 */
	public final function removeUninstallRemovesData()/*: void*/ {
		ilSession::clear(self::KEY_UNINSTALL_REMOVES_DATA);
	}
}
