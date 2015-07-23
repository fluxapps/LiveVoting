<?php

require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteVotingGUI.php');


/**
 * Class ilObjLiveVotingGUI
 *
 * @ilCtrl_Calls      xlvoVotingGUI: xlvoSingleVoteVotingGUI
 *
 */
class xlvoVotingGUI {

	const IDENTIFIER = 'xlvo';

	const CMD_STANDARD = 'add';
	const CMD_CREATE = 'create';
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
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
				$this->tabs->setTabActive(self::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
	}


	protected function add() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, parent::CMD_STANDARD);
		} else {
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, new xlvoVoting());
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function create() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, parent::CMD_STANDARD);
		} else {
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, new xlvoVoting());
			$xlvoVotingFormGUI->setValuesByPost();
			if ($xlvoVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
				$this->ctrl->redirect($this);
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
				$this->ctrl->redirect($this);
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