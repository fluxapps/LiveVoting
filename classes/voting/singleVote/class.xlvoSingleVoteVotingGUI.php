<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingGUI.php');

class xlvoSingleVoteVotingGUI extends xlvoVotingGUI {

	const IDENTIFIER = 'xlvoSingleVote';


	public function __construct() {
		parent::__construct();
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(parent::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
	}


	protected function add() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, parent::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, new xlvoVoting());
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function create() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, parent::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, new xlvoVoting());
			$xlvoSingleVoteVotingFormGUI->setValuesByPost();
			if ($xlvoSingleVoteVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
				$this->ctrl->redirect($this);
			}
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function edit() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[self::IDENTIFIER]));
			$xlvoSingleVoteVotingFormGUI->fillForm();
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function update() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[self::IDENTIFIER]));
			$xlvoSingleVoteVotingFormGUI->setValuesByPost();
			if ($xlvoSingleVoteVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
				$this->ctrl->redirect($this);
			}
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}
}