<?php

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

	/**
	 * @return void
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
