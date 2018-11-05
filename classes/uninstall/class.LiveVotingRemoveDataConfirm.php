<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Utils\LiveVotingTrait;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class LiveVotingRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy LiveVotingRemoveDataConfirm: ilUIPluginRouterGUI
 */
class LiveVotingRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getUninstallRemovesData()/*: ?bool*/ {
		return xlvoConf::getConfig(self::KEY_UNINSTALL_REMOVES_DATA);
	}


	/**
	 * @inheritdoc
	 */
	public function setUninstallRemovesData(/*bool*/
		$uninstall_removes_data)/*: void*/ {
		xlvoConf::set(self::KEY_UNINSTALL_REMOVES_DATA, $uninstall_removes_data);
	}


	/**
	 * @inheritdoc
	 */
	public function removeUninstallRemovesData()/*: void*/ {
		xlvoConf::remove(self::KEY_UNINSTALL_REMOVES_DATA);
	}
}
