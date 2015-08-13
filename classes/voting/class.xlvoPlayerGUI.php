<?php
require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoDisplayPlayerGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoMultiLineInputGUI.php');

/**
 *
 */
class xlvoPlayerGUI {

	const TAB_STANDARD = 'tab_voter';
	const CMD_STANDARD = 'showVoting';
	const CMD_SHOW_VOTING = 'showVoting';
	const CMD_NEXT = 'nextVoting';
	const CMD_PREVIOUS = 'previousVoting';
	const CMD_FREEZE = 'freeze';
	const CMD_RESET = 'resetVotes';
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
		$this->tabs->addTab(self::TAB_STANDARD, $this->pl->txt('player'), $this->ctrl->getLinkTarget($this, self::CMD_STANDARD));
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
	 * @param int $voting_id
	 */
	public function showVoting($voting_id = 0) {

		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('voting'));
		$current_selection_list->setId('xlvo_select');
		$current_selection_list->setTriggerEvent('xlvo_voting');
		$current_selection_list->setUseImages(false);
		$votings = $this->voting_manager->getVotings()->get();
		foreach ($votings as $voting) {
			$current_selection_list->addItem($voting->getTitle(), $voting->getId(), '#');
		}

		$this->toolbar->addText($current_selection_list->getHTML());
		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_back');
		$b->setUrl($this->ctrl->getLinkTarget($this, self::CMD_PREVIOUS));
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_next');
		$b->setUrl($this->ctrl->getLinkTarget($this, self::CMD_NEXT));
		$this->toolbar->addButtonInstance($b);

		$this->toolbar->addSeparator();

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_freeze');
		$b->setUrl($this->ctrl->getLinkTarget(new xlvoVoterGUI(), 'showVoting'));
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_reset');
		$b->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), 'showVoting'));
		$this->toolbar->addButtonInstance($b);

		if ($voting_id == 0) {
			$vo = $this->voting_manager->getVotings()->first();
			$voting = $this->voting_manager->getVoting($vo->getId());
		} else {
			$voting = $this->voting_manager->getVoting($voting_id);
		}

		$display = new xlvoDisplayPlayerGUI($voting);

		$this->tpl->setContent($display->getHTML());
	}


	/**
	 *
	 */
	public function nextVoting() {
		$this->showVoting();
	}


	/**
	 *
	 */
	public function previousVoting() {
		// TODO implement here
	}


	/**
	 *
	 */
	public function resetVotes() {
		// TODO implement here
	}


	/**
	 *
	 */
	public function freeze() {
		// TODO implement here
	}
}