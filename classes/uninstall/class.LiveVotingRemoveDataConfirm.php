<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use LiveVoting\Conf\xlvoConf;
use srag\DIC\DICTrait;

/**
 * Class LiveVotingRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy LiveVotingRemoveDataConfirm: ilUIPluginRouterGUI
 */
class LiveVotingRemoveDataConfirm {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	const CMD_CANCEL = "cancel";
	const CMD_CONFIRM_REMOVE_DATA = "confirmRemoveData";
	const CMD_DEACTIVATE = "deactivate";
	const CMD_SET_KEEP_DATA = "setKeepData";
	const CMD_SET_REMOVE_DATA = "setRemoveData";


	/**
	 * @param bool $plugin
	 */
	public static function saveParameterByClass($plugin = true) {
		$ref_id = filter_input(INPUT_GET, "ref_id");
		self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ref_id", $ref_id);
		self::dic()->ctrl()->setParameterByClass(self::class, "ref_id", $ref_id);

		if ($plugin) {
			$ctype = filter_input(INPUT_GET, "ctype");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ctype", $ctype);
			self::dic()->ctrl()->setParameterByClass(self::class, "ctype", $ctype);

			$cname = filter_input(INPUT_GET, "cname");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "cname", $cname);
			self::dic()->ctrl()->setParameterByClass(self::class, "cname", $cname);

			$slot_id = filter_input(INPUT_GET, "slot_id");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "slot_id", $slot_id);
			self::dic()->ctrl()->setParameterByClass(self::class, "slot_id", $slot_id);

			$plugin_id = filter_input(INPUT_GET, "plugin_id");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "plugin_id", $plugin_id);
			self::dic()->ctrl()->setParameterByClass(self::class, "plugin_id", $plugin_id);

			$pname = filter_input(INPUT_GET, "pname");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "pname", $pname);
			self::dic()->ctrl()->setParameterByClass(self::class, "pname", $pname);
		}
	}


	/**
	 *
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand() {
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
	protected function redirectToPlugins($cmd) {
		self::saveParameterByClass($cmd !== "listPlugins");

		self::dic()->ctrl()->redirectByClass([
			ilAdministrationGUI::class,
			ilObjComponentSettingsGUI::class
		], $cmd);
	}


	/**
	 *
	 */
	protected function cancel() {
		$this->redirectToPlugins("listPlugins");
	}


	/**
	 *
	 */
	protected function confirmRemoveData() {
		self::saveParameterByClass();

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText(self::translate("uninstall_confirm_remove_data"));

		$confirmation->addItem("_", "_", self::translate("uninstall_data"));

		$confirmation->addButton(self::translate("uninstall_remove_data"), self::CMD_SET_REMOVE_DATA);
		$confirmation->addButton(self::translate("uninstall_keep_data"), self::CMD_SET_KEEP_DATA);
		$confirmation->addButton(self::translate("uninstall_deactivate"), self::CMD_DEACTIVATE);
		$confirmation->setCancel(self::translate("cancel"), self::CMD_CANCEL);

		self::output($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deactivate() {
		$this->redirectToPlugins("deactivatePlugin");
	}


	/**
	 *
	 */
	protected function setKeepData() {
		xlvoConf::set(ilLiveVotingPlugin::KEY_UNINSTALL_REMOVE_DATA, false);

		ilUtil::sendInfo(self::translate("uninstall_msg_kept_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}


	/**
	 *
	 */
	protected function setRemoveData() {
		xlvoConf::set(ilLiveVotingPlugin::KEY_UNINSTALL_REMOVE_DATA, true);

		ilUtil::sendInfo(self::translate("uninstall_msg_removed_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}
}
