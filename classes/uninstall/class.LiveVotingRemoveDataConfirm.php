<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use LiveVoting\Conf\xlvoConf;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class LiveVotingRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy LiveVotingRemoveDataConfirm: ilUIPluginRouterGUI
 */
class LiveVotingRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function removeUninstallRemovesData() {
		xlvoConf::remove(self::KEY_UNINSTALL_REMOVES_DATA);
	}


	/**
	 * @inheritdoc
	 */
	public function getUninstallRemovesData() {
		return xlvoConf::getConfig(self::KEY_UNINSTALL_REMOVES_DATA);
	}


	/**
	 * @inheritdoc
	 */
	public function setUninstallRemovesData($uninstall_removes_data) {
		xlvoConf::set(self::KEY_UNINSTALL_REMOVES_DATA, $uninstall_removes_data);
	}
}
