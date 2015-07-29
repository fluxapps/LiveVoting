<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteVotingFormGUI.php');

class xlvoSingleVoteVotingGUI extends xlvoVotingGUI {

	const TAB_ADD = 'tab_singlevote_add';
	const TAB_EDIT = 'tab_singlevote_edit';


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
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[parent::IDENTIFIER]));
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function create() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[parent::IDENTIFIER]));
			$xlvoSingleVoteVotingFormGUI->setValuesByPost();
			if ($xlvoSingleVoteVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
				$this->ctrl->redirect($this, self::CMD_EDIT);
			}
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function edit() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[parent::IDENTIFIER]));
			$xlvoSingleVoteVotingFormGUI->fillForm();
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function update() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[parent::IDENTIFIER]));
			$xlvoSingleVoteVotingFormGUI->setValuesByPost();
			if ($xlvoSingleVoteVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
				$this->ctrl->redirect($this, self::CMD_EDIT);
			}
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}
}