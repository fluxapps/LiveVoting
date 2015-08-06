<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingGUI.php');

class xlvoFreeInputVotingGUI extends xlvoVotingGUI {

	public function __construct() {
		parent::__construct();
	}


	public function executeCommand() {
		$this->tabs->setTabActive(self::TAB_STANDARD);
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
	}


	protected function add() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$this->create();
		}
	}


	protected function create() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);
			$xlvoVoting->setVotingStatus(xlvoVoting::STAT_ACTIVE);
			$xlvoVoting->update();

			$xlvoOption = new xlvoOption();
			$xlvoOption->setVotingId($xlvoVoting->getId());
			$xlvoOption->setType($xlvoVoting->getVotingType());
			$xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
			$xlvoOption->setText('FREE_INPUT');
			$xlvoOption->create();
			
			ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
			$this->ctrl->setParameter(new xlvoVotingGUI(), self::IDENTIFIER, $xlvoVoting->getId());
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_EDIT);
		}
	}


	protected function edit() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);
			$this->ctrl->setParameter(new xlvoVotingGUI(), self::IDENTIFIER, $xlvoVoting->getId());
			ilUtil::sendSuccess($this->pl->txt('voting_updated'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_EDIT);
		}
	}
}