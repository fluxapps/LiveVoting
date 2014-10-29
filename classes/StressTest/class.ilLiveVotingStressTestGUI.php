<?php
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');

/**
 * GUI-Class ilLiveVotingStressTestGUI
 *
 * @author            Fabian Schmid <fabian.schmid@ilub.unibe.ch>
 * @version           $Id:
 *
 * @ilCtrl_Calls
 * @ilCtrl_IsCalledBy
 */
class ilLiveVotingStressTestGUI {

	public function __construct($parent = NULL) {
		global $tpl, $ilCtrl, $ilToolbar, $ilTabs;
		/**
		 * @var $tpl       ilTemplate
		 * @var $ilCtrl    ilCtrl
		 * @var $ilToolbar ilToolbarGUI
		 * @var $ilTabs    ilTabsGUI
		 */
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->parent = $parent;
		$this->toolbar = $ilToolbar;
		$this->tabs_gui = $ilTabs;
		$this->pl = ilLiveVotingPlugin::getInstance();
	}


	public function executeCommand() {
		$cmd = $this->ctrl->getCmd() ? $this->ctrl->getCmd() : 'index';
		$this->performCommand($cmd);

		return true;
	}


	/**
	 * @param $cmd
	 */
	function performCommand($cmd) {
		switch ($cmd) {
			default:
				$this->$cmd();
				break;
		}
	}


	public function index() {
	}


	public function add() {
	}


	public function create() {
	}


	public function edit() {
	}


	public function update() {
	}


	public function conrfirmDelete() {
	}


	public function delete() {
	}
}

?>