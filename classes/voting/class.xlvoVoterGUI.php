<?php
require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoDisplayVotingGUI.php');

/**
 *
 */
class xlvoVoterGUI {

	const TAB_STANDARD = 'tab_voter';
	const CMD_STANDARD = 'showVoting';
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
	 * @var xlvoVoting_manager
	 */
	protected $voting_manager;


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
		$this->voting_manager = new xlvoVotingManager();
		$this->obj_id = ilObject2::_lookupObjId($_GET['ref_id']);
	}


	public function executeCommand() {
		$this->tabs->addTab(self::TAB_STANDARD, $this->pl->txt('voter'), $this->ctrl->getLinkTarget($this, self::CMD_STANDARD));
		$this->tabs->setTabActive(self::TAB_STANDARD);
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
	}


	/**
	 * @param null $voting_id
	 *
	 * @return string
	 */
	public function showVoting($voting_id = NULL) {
		/**
		 * @var $xlvoVoting xlvoVoting
		 */
		$xlvoVoting = $this->voting_manager->getVoting(40);
		$display = new xlvoDisplayVotingGUI($xlvoVoting);

		$this->tpl->setContent($display->getHTML());
	}


	/**
	 * @param void $pin
	 */
	public function accessVoting($pin = NULL) {
		// TODO implement here
	}


	/**
	 * @param xlvoVote $vote
	 *
	 * @return xlvoVote
	 */
	public function vote(xlvoVote $vote) {
		/**
		 * @var xlvoVote $vote
		 */
		$xlvoVote = new xlvoVote();
		$xlvoVote->setOptionId($vote->getOptionId());
		$xlvoVote->setId($vote->getId());
		$xlvoVote->setStatus($vote->getStatus());
		$xlvoVote->setFreeInput($vote->getFreeInput());
		$vote = $this->voting_manager->vote($xlvoVote);
		if ($vote instanceof xlvoVote) {
			return $vote;
		} else {
			$vote = new xlvoVote();
			$vote->setStatus(xlvoVote::STAT_INACTIVE);
			$vote->setVotingId(20);
			$vote->setOptionId($vote->getOptionId());
			return $vote;
		}
		return $vote;
	}

}