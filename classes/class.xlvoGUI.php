<?php

use LiveVoting\Conf\xlvoConf;

/**
 * Class xlvoGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoGUI {

	const CMD_STANDARD = 'index';
	const CMD_ADD = 'add';
	const CMD_SAVE = 'save';
	const CMD_CREATE = 'create';
	const CMD_EDIT = 'edit';
	const CMD_UPDATE = 'update';
	const CMD_CONFIRM = 'confirmDelete';
	const CMD_DELETE = 'delete';
	const CMD_CANCEL = 'cancel';
	const CMD_VIEW = 'view';


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilToolbar;
		/**
		 * @var $ilCtrl    \ilCtrl
		 * @var $ilTabs    \ilTabsGUI
		 * @var $tpl       |ilTemplate
		 * @var $ilToolbar |ilToolbarGUI
		 */
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->toolbar = $ilToolbar;
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->is_api_call = ($this->ctrl->getTargetScript() == xlvoConf::getFullApiURL());
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		\LiveVoting\Js\xlvoJs::getInstance()->name('Main')->init();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
		if ($this->is_api_call) {
			$this->tpl->show();
		}
	}


	protected function cancel() {
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}
}

?>
