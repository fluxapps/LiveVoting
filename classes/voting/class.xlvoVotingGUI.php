<?php

require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Services/Utilities/classes/class.ilConfirmationGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingTableGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteVotingGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/freeInput/class.xlvoFreeInputVotingGUI.php');

/**
 *
 * Class xlvoVotingGUI
 *
 * @author            Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_Calls      xlvoVotingGUI: xlvoSingleVoteVotingGUI, xlvoFreeInputVotingGUI
 *
 */
class xlvoVotingGUI {

	const IDENTIFIER = 'xlvoVot';
	const CMD_STANDARD = 'content';
	const CMD_CONTENT = 'content';
	const CMD_ADD = 'add';
	const CMD_CREATE = 'create';
	const CMD_EDIT = 'edit';
	const CMD_UPDATE = 'update';
	const CMD_CONFIRM_DELETE = 'confirmDelete';
	const CMD_DELETE = 'delete';
	const CMD_CONFIRM_RESET = 'confirmReset';
	const CMD_RESET = 'reset';
	const CMD_CONFIRM_RESET_ALL = 'confirmResetAll';
	const CMD_RESET_ALL = 'resetAll';
	const CMD_CANCEL = 'cancel';
	const CMD_BACK = 'back';
	/**
	 * @var ilTemplate
	 */
	public $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;
	/**
	 * @var ilObjLiveVotingAccess
	 */
	protected $access;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;
	/**
	 * @var ilObjUser
	 */
	protected $usr;
	/**
	 * @var int
	 */
	protected $obj_id;
	/**
	 * @var xlvoVotingManager
	 */
	protected $voting_manager;


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilUser, $ilToolbar;

		/**
		 * @var $tpl       ilTemplate
		 * @var $ilCtrl    ilCtrl
		 * @var $ilTabs    ilTabsGUI
		 * @var $ilUser    ilObjUser
		 * @var $ilToolbar ilToolbarGUI
		 */
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->usr = $ilUser;
		$this->toolbar = $ilToolbar;
		$this->access = new ilObjLiveVotingAccess();
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->voting_manager = new xlvoVotingManager();
		$this->obj_id = ilObject2::_lookupObjId($_GET['ref_id']);
	}


	public function executeCommand() {
		$this->tabs->setTabActive(self::CMD_CONTENT);

		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			case 'xlvosinglevotevotinggui':
				$this->ctrl->forwardCommand(new xlvoSingleVoteVotingGUI());
				break;
			case 'xlvofreeinputvotinggui':
				$this->ctrl->forwardCommand(new xlvoFreeInputVotingGUI());
				break;
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
	}


	/**
	 *
	 * Switch for redirecting voting types to corresponding sub GUI.
	 *
	 * @param $voting_type
	 * @param $cmd
	 */
	private function redirectToSubGUI($voting_type, $cmd) {
		switch ($voting_type) {
			case xlvoVotingType::SINGLE_VOTE:
				$this->ctrl->redirect(new xlvoSingleVoteVotingGUI(), $cmd);
				break;
			case xlvoVotingType::FREE_INPUT:
				$this->ctrl->redirect(new xlvoFreeInputVotingGUI(), $cmd);
				break;
		}
	}


	protected function content() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
		} else {
			if ($this->access->hasWriteAccess()) {
				$b = ilLinkButton::getInstance();
				$b->setCaption('rep_robj_xlvo_add_voting');
				$b->setUrl($this->ctrl->getLinkTarget(new xlvoVotingGUI(), self::CMD_ADD));
				$this->toolbar->addButtonInstance($b);

				$b = ilLinkButton::getInstance();
				$b->setCaption('rep_robj_xlvo_reset_all_votes');
				$b->setUrl($this->ctrl->getLinkTarget(new xlvoVotingGUI(), self::CMD_CONFIRM_RESET_ALL));
				$this->toolbar->addButtonInstance($b);

				$xlvoVotingTableGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);
				$this->tpl->setContent($xlvoVotingTableGUI->getHTML());
			}
		}
	}


	protected function add() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, new xlvoVoting());
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function create() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, new xlvoVoting());
			$xlvoVotingFormGUI->setValuesByPost();
			if ($xlvoVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('msg_success_voting_created'), true);
				$voting = $xlvoVotingFormGUI->getVoting();
				$this->ctrl->setParameter(new xlvoVotingGUI(), self::IDENTIFIER, $voting->getId());
				$this->redirectToSubGUI($voting->getVotingType(), self::CMD_ADD);
			}
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function edit() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, xlvoVoting::find($_GET[self::IDENTIFIER]));
			$xlvoVotingFormGUI->fillForm();
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function update() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, xlvoVoting::find($_GET[self::IDENTIFIER]));
			$xlvoVotingFormGUI->setValuesByPost();
			if ($xlvoVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('msg_success_voting_updated'), true);
				$voting = $xlvoVotingFormGUI->getVoting();
				$this->ctrl->setParameter(new xlvoVotingGUI(), self::IDENTIFIER, $voting->getId());
				$this->redirectToSubGUI($voting->getVotingType(), self::CMD_EDIT);
			}
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function confirmDelete() {
		if (! $this->access->hasDeleteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {

			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);

			if ($xlvoVoting->getObjId() == $this->getObjId()) {
				ilUtil::sendQuestion($this->pl->txt('confirm_delete_voting'), true);
				$confirm = new ilConfirmationGUI();
				$confirm->addItem(self::IDENTIFIER, $xlvoVoting->getId(), $xlvoVoting->getTitle());
				$confirm->setFormAction($this->ctrl->getFormAction($this));
				$confirm->setCancel($this->pl->txt('cancel'), self::CMD_CANCEL);
				$confirm->setConfirm($this->pl->txt('delete'), self::CMD_DELETE);

				$this->tpl->setContent($confirm->getHTML());
			} else {
				ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
		}
	}


	protected function delete() {
		if (! $this->access->hasDeleteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {

			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_POST[self::IDENTIFIER]);

			if ($xlvoVoting->getObjId() == $this->getObjId()) {
				/**
				 * @var $options xlvoOption[]
				 */
				$options = xlvoOption::where(array( 'voting_id' => $xlvoVoting->getId() ))->get();
				foreach ($options as $option) {
					$option->delete();
				}
				/**
				 * @var $votes xlvoVote[]
				 */
				$votes = xlvoVote::where(array( 'voting_id' => $xlvoVoting->getId() ))->get();
				foreach ($votes as $vote) {
					$vote->delete();
				}
				$xlvoVoting->delete();
				$this->cancel();
			} else {
				ilUtil::sendFailure($this->pl->txt('delete_failed'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
		}
	}


	protected function confirmReset() {

		if (! $this->access->hasDeleteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {

			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);

			if ($xlvoVoting->getObjId() == $this->getObjId()) {
				ilUtil::sendQuestion($this->pl->txt('confirm_reset_voting'), true);
				$confirm = new ilConfirmationGUI();
				$confirm->addItem(self::IDENTIFIER, $xlvoVoting->getId(), $xlvoVoting->getTitle());
				$confirm->setFormAction($this->ctrl->getFormAction($this));
				$confirm->setCancel($this->pl->txt('cancel'), self::CMD_CANCEL);
				$confirm->setConfirm($this->pl->txt('reset'), self::CMD_RESET);

				$this->tpl->setContent($confirm->getHTML());
			} else {
				ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
		}
	}


	protected function reset() {
		if (! $this->access->hasDeleteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_POST[self::IDENTIFIER]);

			if ($xlvoVoting->getObjId() == $this->getObjId()) {

				/**
				 * @var $votes xlvoVote[]
				 */
				$votes = xlvoVote::where(array( 'voting_id' => $xlvoVoting->getId() ))->get();
				foreach ($votes as $vote) {
					$vote->delete();
				}
				$this->cancel();
			} else {
				ilUtil::sendFailure($this->pl->txt('reset_failed'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
		}
	}


	protected function confirmResetAll() {
		if (! $this->access->hasDeleteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			ilUtil::sendQuestion($this->pl->txt('confirm_reset_all_votings'), true);
			$confirm = new ilConfirmationGUI();
			/**
			 * @var $votings xlvoVoting[]
			 */
			$votings = xlvoVoting::where(array( 'obj_id' => $this->getObjId() ))->get();
			$num_votes = 0;
			foreach ($votings as $voting) {
				$num_votes += xlvoVote::where(array( 'voting_id' => $voting->getId() ))->count();
			}
			$confirm->addItem(self::IDENTIFIER, 0, $this->pl->txt('confirm_number_of_votes') . ": " . $num_votes);
			$confirm->setFormAction($this->ctrl->getFormAction($this));
			$confirm->setCancel($this->pl->txt('cancel'), self::CMD_CANCEL);
			$confirm->setConfirm($this->pl->txt('reset_all'), self::CMD_RESET_ALL);

			$this->tpl->setContent($confirm->getHTML());
		}
	}


	protected function resetAll() {
		if (! $this->access->hasDeleteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			/**
			 * @var $votings xlvoVoting[]
			 */
			$votings = xlvoVoting::where(array( 'obj_id' => $this->getObjId() ))->get();
			foreach ($votings as $voting) {
				/**
				 * @var $votes xlvoVote[]
				 */
				$votes = xlvoVote::where(array( 'voting_id' => $voting->getId() ))->get();
				foreach ($votes as $vote) {
					$vote->delete();
				}
			}

			$this->cancel();
		}
	}


	protected function cancel() {
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	protected function applyFilter() {
		$xlvoVotingGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);
		$xlvoVotingGUI->writeFilterToSession();
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	protected function resetFilter() {
		$xlvoVotingTableGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);
		$xlvoVotingTableGUI->resetFilter();
		$xlvoVotingTableGUI->resetOffset();
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	/**
	 * @return int
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}
}