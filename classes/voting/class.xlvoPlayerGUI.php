<?php
require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Services/UIComponent/Button/classes/class.ilLinkButton.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoDisplayPlayerGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoPlayer.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoMultiLineInputGUI.php');

/**
 *
 */
class xlvoPlayerGUI {

	const TAB_STANDARD = 'tab_voter';
	const IDENTIFIER = 'xlvoVot';
	const CMD_STANDARD = 'startVoting';
	const CMD_SHOW_VOTING = 'showVoting';
	const CMD_START_VOTING = 'startVoting';
	const CMD_NEXT = 'nextVoting';
	const CMD_PREVIOUS = 'previousVoting';
	const CMD_FREEZE = 'freeze';
	const CMD_UNFREEZE = 'unfreeze';
	const CMD_RESET = 'resetVotes';
	const CMD_TERMINATE = 'terminate';
	const CMD_END_OF_VOTING = 'endOfVoting';
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


	public function startVoting() {
		$vo = $this->voting_manager->getVotings()->first();
		if ($vo == NULL) {
			ilUtil::sendInfo($this->pl->txt('msg_no_voting_available'), true);
			$this->ctrl->redirect(new xlvoVotingGUI(), xlvoVotingGUI::CMD_STANDARD);
		} else {
			$this->setActiveVoting($vo->getId());
			$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $vo->getId());
			$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_SHOW_VOTING);
		}
	}


	/**
	 * @param null $voting_id
	 *
	 * @return string
	 */
	public function showVoting($voting_id = NULL) {

		if ($voting_id == NULL) {
			if ($_GET[self::IDENTIFIER] != NULL) {
				$voting_id = $_GET[self::IDENTIFIER];
			} else {
				$voting_id = 0;
			}
		}

		if ($voting_id != 0) {
			$voting = $this->voting_manager->getVoting($voting_id);

			$isAvailable = $this->isAvailable($voting->getObjId());
			$isStarted = $this->getPlayer($voting->getObjId())->getStatus();

			if ($isAvailable && $isStarted) {

				$this->initToolbar();

				$this->setActiveVoting($voting->getId());

				$display = new xlvoDisplayPlayerGUI($voting);

				$this->tpl->setContent($display->getHTML());

				return $display->getHTML();
			} else {
				ilUtil::sendFailure($this->pl->txt('msg_voting_not_available'), false);
			}
		} else {
			ilUtil::sendFailure($this->pl->txt('msg_voting_not_available'), false);
		}
	}


	public function setActiveVoting($voting_id) {
		$xlvoVoting = xlvoVoting::find($voting_id);
		$arr_length = xlvoPlayer::where(array( 'obj_id' => $xlvoVoting->getObjId() ))->count();
		if ($arr_length <= 0) {
			$xlvoPlayer = new xlvoPlayer();
			$xlvoPlayer->setObjId($xlvoVoting->getObjId());
			$xlvoPlayer->setActiveVoting($voting_id);
			$xlvoPlayer->setReset(xlvoPlayer::RESET_OFF);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_RUNNING);
			$xlvoPlayer->create();
		} else {
			$xlvoPlayer = xlvoPlayer::where(array( 'obj_id' => $xlvoVoting->getObjId() ))->first();
			$xlvoPlayer->setActiveVoting($voting_id);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_RUNNING);
			$xlvoPlayer->update();
		}
	}


	public function getActiveVoting($obj_id) {
		$xlvoPlayer = xlvoPlayer::where(array( 'obj_id' => $obj_id ))->first();

		return $xlvoPlayer->getActiveVoting();
	}


	public function nextVoting() {
		$xlvoPlayer = xlvoPlayer::where(array( 'obj_id' => $this->obj_id ))->first();
		$voting_id_current = $xlvoPlayer->getActiveVoting();
		$votings = $this->voting_manager->getVotings()->where(array( 'voting_status' => xlvoVoting::STAT_ACTIVE ))->getArray();
		$voting_last = $this->voting_manager->getVotings()->where(array( 'voting_status' => xlvoVoting::STAT_ACTIVE ))->last();

		$voting_id_next = $voting_id_current;
		$get_next_elem = false;
		foreach ($votings as $key => $voting) {
			if ($get_next_elem) {
				$voting_id_next = $voting['id'];
				break;
			}
			if ($voting['id'] == $voting_id_current) {
				$get_next_elem = true;
			}
		}

		if ($voting_id_current == $voting_last->getId()) {

			// TODO show voting finished screen
			$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_END_OF_VOTING);
		}
		$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $voting_id_next);
		$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_SHOW_VOTING);
	}


	public function previousVoting() {
		$xlvoPlayer = xlvoPlayer::where(array( 'obj_id' => $this->obj_id ))->first();
		$voting_id_current = $xlvoPlayer->getActiveVoting();
		$votings = array_reverse($this->voting_manager->getVotings()->where(array( 'voting_status' => xlvoVoting::STAT_ACTIVE ))->getArray());
		$voting_first = $this->voting_manager->getVotings()->first();

		$voting_id_previous = $voting_id_current;
		$get_next_elem = false;
		foreach ($votings as $key => $voting) {
			if ($get_next_elem) {
				$voting_id_previous = $voting['id'];
				break;
			}
			if ($voting['id'] == $voting_id_current) {
				$get_next_elem = true;
			}
		}

		if ($voting_id_current == $voting_first->getId()) {
			ilUtil::sendInfo($this->pl->txt('msg_no_previous_voting'), true);
		}

		$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $voting_id_previous);
		$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_SHOW_VOTING);
	}


	public function endOfVoting() {

		$reset_voting_id = 0;
		$xlvoPlayer = xlvoPlayer::where(array( 'obj_id' => $this->obj_id ))->first();
		$xlvoPlayer->setActiveVoting($reset_voting_id);
		$xlvoPlayer->setStatus(xlvoPlayer::STAT_END_VOTING);
		$xlvoPlayer->update();

		$this->setContentEndOfVoting();
	}


	public function resetVotes($voting_id) {
		$xlvoVoting = xlvoVoting::find($voting_id);
		$xlvoPlayer = $this->getPlayer($xlvoVoting->getObjId());
		$xlvoPlayer->setReset(xlvoPlayer::RESET_ON);
		$xlvoPlayer->update();

		$this->voting_manager->deleteVotesForVoting($voting_id);

		// wait 5 seconds. voter pages can be updated during this time.
		sleep(5);

		$xlvoPlayer->setReset(xlvoPlayer::RESET_OFF);
		$xlvoPlayer->update();
	}


	public function freeze($obj_id) {
		$xlvoVotingConfig = xlvoVotingConfig::find($obj_id);
		$xlvoVotingConfig->setFrozen(true);
		$xlvoVotingConfig->update();
	}


	public function unfreeze($obj_id) {
		$xlvoVotingConfig = xlvoVotingConfig::find($obj_id);
		$xlvoVotingConfig->setFrozen(false);
		$xlvoVotingConfig->update();
	}


	public function terminate() {
		$this->unfreeze($this->obj_id);
		$xlvoPlayer = $this->getPlayer($this->obj_id);
		$xlvoPlayer->setStatus(xlvoPlayer::STAT_STOPPED);
		$xlvoPlayer->update();
		$this->ctrl->redirect(new xlvoVotingGUI(), xlvoVotingGUI::CMD_STANDARD);
	}


	public function getPlayer($obj_id) {
		return xlvoPlayer::where(array( 'obj_id' => $obj_id ))->first();
	}


	public function isAvailable($obj_id) {
		$xlvoVotingConfig = xlvoVotingConfig::find($obj_id);

		$terminable = $xlvoVotingConfig->isTerminable();

		if (! $terminable) {
			return true;
		} else {
			$format = 'Y-m-d H:i:s';
			$start_date = strtotime($xlvoVotingConfig->getStartDate());
			$end_date = strtotime($xlvoVotingConfig->getEndDate());
			$now = strtotime(date($format));
			if ($start_date <= $now && $end_date >= $now) {
				return true;
			} else {
				return false;
			}
		}
	}


	protected function initToolbar() {
		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('voting'));
		$current_selection_list->setId('xlvo_select');
		$current_selection_list->setTriggerEvent('xlvo_voting');
		$current_selection_list->setUseImages(false);
		$votings = $this->voting_manager->getVotings()->where(array( 'voting_status' => xlvoVoting::STAT_ACTIVE ))->get();
		foreach ($votings as $voting) {
			$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $voting->getId());
			$current_selection_list->addItem($voting->getTitle(), $voting->getId(), $this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_SHOW_VOTING));
		}
		$this->toolbar->addText($current_selection_list->getHTML());

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_back');
		$b->setUrl($this->ctrl->getLinkTarget($this, self::CMD_PREVIOUS));
		$b->setId('btn-previous');
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_next');
		$b->setUrl($this->ctrl->getLinkTarget($this, self::CMD_NEXT));
		$b->setId('btn-next');
		$this->toolbar->addButtonInstance($b);

		$this->toolbar->addSeparator();

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_freeze');
		$b->setUrl('#');
		$b->setId('btn-freeze');
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_unfreeze');
		$b->setUrl('#');
		$b->setId('btn-unfreeze');
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_terminate');
		$b->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_TERMINATE));
		$b->setId('btn-terminate');
		$this->toolbar->addButtonInstance($b);

		$this->toolbar->addSeparator();

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_reset');
		$b->setUrl('#');
		$b->setId('btn-reset');
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_hide_results');
		$b->setUrl('#');
		$b->setId('btn-hide-results');
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setCaption('rep_robj_xlvo_show_results');
		$b->setUrl('#');
		$b->setId('btn-show-results');
		$this->toolbar->addButtonInstance($b);
	}


	protected function setContentEndOfVoting() {

		$bb = ilLinkButton::getInstance();
		$bb->setCaption('rep_robj_xlvo_back_to_voting');
		$bb->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_VOTING));
		$bb->setId('btn-back_to_voting');

		$bt = ilLinkButton::getInstance();
		$bt->setCaption('rep_robj_xlvo_terminate');
		$bt->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_TERMINATE));
		$bt->setId('btn-terminate');

		$this->toolbar->addButtonInstance($bb);
		$this->toolbar->addButtonInstance($bt);

		$this->tpl->setContent($this->pl->txt('msg_end_of_voting') . '<br><br>' . $bb->render() . ' ' . $bt->render());

	}
}