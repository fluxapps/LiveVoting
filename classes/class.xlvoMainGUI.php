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

		//		$this->tabs->addTab(self::TAB_SETTINGS, $this->pl->txt('tab_' . self::TAB_SETTINGS), $this->ctrl->getLinkTarget(new xlvoConfGUI()));
		//		$this->tabs->addTab(self::TAB_SYSTEM_ACCOUNTS, $this->pl->txt('tab_'
		//			. self::TAB_SYSTEM_ACCOUNTS), $this->ctrl->getLinkTarget(new xoctSystemAccountGUI()));
		//		$this->tabs->addTab(self::TAB_PUBLICATION_USAGE, $this->pl->txt('tab_'
		//			. self::TAB_PUBLICATION_USAGE), $this->ctrl->getLinkTarget(new xoctPublicationUsageGUI()));
		//		$this->tabs->addTab(self::TAB_EXPORT, $this->pl->txt('tab_' . self::TAB_EXPORT), $this->ctrl->getLinkTarget(new xoctConfExportGUI()));
		//
		switch ($nextClass) {

			default:
				//						$this->tabs->setTabActive(self::TAB_SETTINGS);
				$xlvoConfGUI = new xlvoConfGUI();
				$this->ctrl->forwardCommand($xlvoConfGUI);
				break;
		}
	}


	protected function index() {
		// TODO: Implement index() method.
	}


	protected function add() {
		// TODO: Implement add() method.
	}


	protected function create() {
		// TODO: Implement create() method.
	}


	protected function edit() {
		// TODO: Implement edit() method.
	}


	protected function update() {
		// TODO: Implement update() method.
	}


	protected function confirmDelete() {
		// TODO: Implement confirmDelete() method.
	}


	protected function delete() {
		// TODO: Implement delete() method.
	}
}

?>
