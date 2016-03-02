<?php
require_once('class.xlvoGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConfGUI.php');

/**
 * Class xlvoMainGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy xlvoMainGUI : ilLiveVotingConfigGUI
 */
class xlvoMainGUI extends xlvoGUI {

	const TAB_SETTINGS = 'settings';
	const TAB_SYSTEM_ACCOUNTS = 'system_accounts';
	const TAB_PUBLICATION_USAGE = 'publication_usage';
	const TAB_EXPORT = 'export';


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilToolbar;
		/**
		 * @var $ilCtrl    ilCtrl
		 * @var $ilTabs    ilTabsGUI
		 * @var $tpl       ilTemplate
		 * @var $ilToolbar ilToolbarGUI
		 */
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->toolbar = $ilToolbar;
		$this->pl = ilLiveVotingPlugin::getInstance();
	}


	/**
	 * @return bool
	 */
	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$xlvoConfGUI = new xlvoConfGUI();
				$this->ctrl->forwardCommand($xlvoConfGUI);
				break;
		}
	}
}

?>
