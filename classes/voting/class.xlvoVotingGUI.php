<?php

require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteVotingGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/freeInput/class.xlvoFreeInputVotingGUI.php');

/**
 * Class ilObjLiveVotingGUI
 *
 * @ilCtrl_Calls      xlvoVotingGUI: xlvoSingleVoteVotingGUI, xlvoFreeInputVotingGUI
 *
 */
class xlvoVotingGUI {

	const IDENTIFIER = 'xlvoVot';
	const TAB_STANDARD = 'tab_voting';
	const TAB_ADD = 'tab_voting_add';
	const TAB_EDIT = 'tab_voting_edit';
	const CMD_STANDARD = 'add';
	const CMD_ADD = 'add';
	const CMD_CREATE = 'create';
	const CMD_EDIT = 'edit';
	const CMD_UPDATE = 'update';
	const CMD_CANCEL = 'cancel';
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
	 * @var ilUser
	 */
	protected $usr;
	/**
	 * @var int
	 */
	protected $obj_id;
	/**
	 * @var xlvoVotingManager
	 */
	protected $votingManager;


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilUser, $ilToolbar;

		/**
		 * @var $tpl       ilTemplate
		 * @var $ilCtrl    ilCtrl
		 * @var $ilTabs    ilTabsGUI
		 * @var $ilUser    ilUser
		 * @var $ilToolbar ilToolbarGUI
		 */
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->usr = $ilUser;
		$this->toolbar = $ilToolbar;
		$this->access = new ilObjLiveVotingAccess();
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->votingManager = new xlvoVotingManager();
		$this->obj_id = ilObject2::_lookupObjId($_GET['ref_id']);
	}


	public function executeCommand() {
		$this->tabs->addTab(self::TAB_STANDARD, $this->pl->txt('voting'), $this->ctrl->getLinkTarget($this, self::CMD_ADD));
		$this->tabs->setTabActive(self::TAB_STANDARD);
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
				ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
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
				ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
				$voting = $xlvoVotingFormGUI->getVoting();
				$this->ctrl->setParameter(new xlvoVotingGUI(), self::IDENTIFIER, $voting->getId());
				$this->redirectToSubGUI($voting->getVotingType(), self::CMD_EDIT);
			}
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function cancel() {
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	protected function reset() {
		// TODO implement here
	}


	private function redirectToSubGUI($voting_type, $cmd) {
		switch ($voting_type) {
			case xlvoVotingType::SINGLE_VOTE:
				$this->ctrl->redirect(new xlvoSingleVoteVotingGUI(), $cmd);
				break;
			case xlvoVotingType::FREE_INPUT:
				$this->ctrl->redirect(new xlvoFreeInputVotingGUI(), $cmd);
				break;
			// TODO add other types
		}
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