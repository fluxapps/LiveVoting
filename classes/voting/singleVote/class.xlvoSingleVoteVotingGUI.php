<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteVotingFormGUI.php');

/**
 * Class xlvoSingleVoteVotingGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoSingleVoteVotingGUI extends xlvoVotingGUI {

	public function __construct() {
		parent::__construct();
	}


	public function executeCommand() {
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
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[parent::IDENTIFIER]));
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function create() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[parent::IDENTIFIER]));
			$xlvoSingleVoteVotingFormGUI->setValuesByPost();
			if ($xlvoSingleVoteVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('msg_success_voting_created'), true);
				$this->ctrl->redirect($this, self::CMD_EDIT);
			}
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function edit() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[parent::IDENTIFIER]));
			$xlvoSingleVoteVotingFormGUI->fillForm();
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function update() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
		} else {
			$xlvoSingleVoteVotingFormGUI = new xlvoSingleVoteVotingFormGUI($this, xlvoVoting::find($_GET[parent::IDENTIFIER]));
			$xlvoSingleVoteVotingFormGUI->setValuesByPost();
			if ($xlvoSingleVoteVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('msg_success_voting_updated'), true);
				$this->ctrl->redirect($this, self::CMD_EDIT);
			}
			$this->tpl->setContent($xlvoSingleVoteVotingFormGUI->getHTML());
		}
	}


	protected function back() {
		$this->ctrl->saveParameter(new xlvoVotingGUI(), xlvoVotingGUI::IDENTIFIER);
		$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_EDIT);
	}


	protected function cancel() {
		$this->ctrl->redirect(new xlvoVotingGUI(), self::CMD_STANDARD);
	}
}