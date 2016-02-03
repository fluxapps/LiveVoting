<?php
require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Services/UIComponent/Button/classes/class.ilLinkButton.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/display/class.xlvoDisplayPlayerGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voter/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Option/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayer.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoMultiLineInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoLinkButton.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConf.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/lib/QrCode-master/src/QrCode.php');
use Endroid\QrCode\QrCode;

/**
 * Class xlvoPlayerGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoPlayerGUI {

	const TAB_STANDARD = 'tab_content';
	const IDENTIFIER = 'xlvoVot';
	const CMD_STANDARD = 'startOfVoting';
	const CMD_SHOW_VOTING = 'showVoting';
	const CMD_START_VOTING = 'startVoting';
	const CMD_NEXT = 'nextVoting';
	const CMD_PREVIOUS = 'previousVoting';
	const CMD_FREEZE = 'freeze';
	const CMD_UNFREEZE = 'unfreeze';
	const CMD_RESET = 'resetVotes';
	const CMD_TERMINATE = 'terminate';
	const CMD_END_OF_VOTING_SCREEN = 'endOfVotingScreen';
	const CMD_START_OF_VOTING_SCREEN = 'startOfVotingScreen';
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


	/**
	 *
	 */
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
		$this->tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/default.css');
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				if ($this->access->hasWriteAccess()) {
					xlvoInitialisation::saveContext(xlvoInitialisation::CONTEXT_ILIAS);
					$cmd = $this->ctrl->getCmd(self::CMD_START_OF_VOTING_SCREEN);
					$this->{$cmd}();
					break;
				} else {
					ilUtil::sendFailure(ilLiveVotingPlugin::getInstance()->txt('permission_denied_write'), true);
					break;
				}
		}
	}


	/**
	 * Start Voting and redirect to first Voting.
	 * This function gets the first Voting of the list of active votings. The Player is set to status running.
	 * Redirects to the first Voting view.
	 * The function is called via LinkButtons on the startOfVotingScreen and endOfVotingScreen.
	 */
	public function startVoting() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
		} else {
			/**
			 * @var xlvoVoting $vo
			 */
			$vo = $this->voting_manager->getActiveVotings($this->obj_id)->first();
			if ($vo == NULL) {
				$this->ctrl->redirect(new xlvoVotingGUI(), xlvoVotingGUI::CMD_STANDARD);
			} else {
				$this->setActiveVoting($vo->getId());
				$this->freeze($this->obj_id);
				$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $vo->getId());
				$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_SHOW_VOTING);
			}
		}
	}


	/**
	 * @param null $voting_id
	 *
	 * @return string
	 * @throws xlvoVotingManagerException
	 */
	public function showVoting($voting_id = NULL) {
		global $ilLog;

		if ($voting_id == NULL) {
			if ($_GET[self::IDENTIFIER] != NULL) {
				$voting_id = $_GET[self::IDENTIFIER];
			} else {
				$voting_id = 0;
			}
		}

		if ($voting_id != 0) {
			try {
				/**
				 * @var xlvoVoting $xlvoVoting
				 */
				$xlvoVoting = $this->voting_manager->getVoting($voting_id);
			} catch (xlvoVotingManagerException $e) {
				ilUtil::sendFailure($this->pl->txt('error_load_voting_failed'), true);

				return $this->tpl->getMessageHTML($this->pl->txt('error_load_voting_failed'), 'failure');
			}

			if (!$this->access->hasWriteAccessForObject($xlvoVoting->getObjId(), $this->usr->getId())) {
				ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);

				return $this->tpl->getMessageHTML($this->pl->txt('permission_denied_write'), 'failure');
			} else {

				/**
				 * @var boolean $isAvailable
				 */
				$isAvailable = $this->voting_manager->isVotingAvailable($xlvoVoting->getObjId());

				try {
					/**
					 * @var xlvoPlayer $xlvoPlayer
					 */
					$xlvoPlayer = $this->voting_manager->getPlayer($xlvoVoting->getObjId());
				} catch (xlvoVotingManagerException $e) {
					ilUtil::sendFailure($this->pl->txt('msg_voting_not_available'), false);

					return $this->tpl->getMessageHTML($this->pl->txt('msg_voting_not_available'), 'failure');
				}

				$isRunning = $xlvoPlayer->getStatus();

				if ($isAvailable && $isRunning == xlvoPlayer::STAT_RUNNING) {

					$this->initToolbar();

					$this->setActiveVoting($xlvoVoting->getId());

					$display = new xlvoDisplayPlayerGUI($xlvoVoting);

					$this->tpl->setContent($display->getHTML());

					return $display->getHTML();
				} else {
					ilUtil::sendFailure($this->pl->txt('msg_voting_not_available'), false);

					return $this->tpl->getMessageHTML($this->pl->txt('error_voting_terminated'), 'failure');
				}
			}
		} else {
			ilUtil::sendFailure($this->pl->txt('msg_voting_not_available'), false);

			return $this->tpl->getMessageHTML($this->pl->txt('msg_voting_not_available'), 'failure');
		}
	}


	/**
	 * @param $voting_id
	 */
	public function setActiveVoting($voting_id) {
		$this->voting_manager->setActiveVoting($voting_id);
	}


	/**
	 * @param $obj_id
	 *
	 * @return int
	 */
	public function getActiveVoting($obj_id) {
		return $this->voting_manager->getActiveVoting($obj_id);
	}


	/**
	 * Redirect to next Voting.
	 */
	public function nextVoting() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
		} else {
			$voting_id_current = $this->getActiveVoting($this->obj_id);

			/**
			 * @var xlvoVoting[] $votings
			 */
			$votings = $this->voting_manager->getActiveVotings($this->obj_id)->getArray();
			/**
			 * @var xlvoVoting $voting_last
			 */
			$voting_last = $this->voting_manager->getActiveVotings($this->obj_id)->last();

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
				$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_END_OF_VOTING_SCREEN);
			}

			$this->freeze($this->obj_id);
			$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $voting_id_next);
			$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_SHOW_VOTING);
		}
	}


	/**
	 * Redirect to previous Voting.
	 */
	public function previousVoting() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
		} else {
			$voting_id_current = $this->getActiveVoting($this->obj_id);
			/**
			 * @var xlvoVoting[] $votings
			 */
			$votings = array_reverse($this->voting_manager->getActiveVotings($this->obj_id)->getArray());
			/**
			 * @var xlvoVoting $voting_first
			 */
			$voting_first = $this->voting_manager->getActiveVotings($this->obj_id)->first();

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
				$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_START_OF_VOTING_SCREEN);
			}

			$this->freeze($this->obj_id);
			$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $voting_id_previous);
			$this->ctrl->redirect(new xlvoPlayerGUI(), self::CMD_SHOW_VOTING);
		}
	}


	/**
	 * Display the Page before the first Voting.
	 * PIN and QR are displayed.
	 * Voting can be started or terminated in this view.
	 */
	public function startOfVotingScreen() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
		} else {
			try {
				/**
				 * @var xlvoPlayer $xlvoPlayer
				 */
				$xlvoPlayer = $this->voting_manager->getPlayer($this->obj_id);
				$xlvoPlayer->setStatus(xlvoPlayer::STAT_START_VOTING);
				$this->voting_manager->updatePlayer($xlvoPlayer);
			} catch (xlvoVotingManagerException $e) {
				/**
				 * @var xlvoVoting $vo
				 */
				$vo = $this->voting_manager->getActiveVotings($this->obj_id)->first();
				if ($vo instanceof xlvoVoting) {
					$this->setActiveVoting($vo->getId());
				} else {
					$this->voting_manager->createPlayer($this->obj_id);
				}
			}

			$this->setContentStartOfVoting();
		}
	}


	/**
	 * Display the Page after last Voting. End of the Voting.
	 * Voting can be restarted or terminated in this view.
	 */
	public function endOfVotingScreen() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
		} else {
			/**
			 * @var xlvoPlayer $xlvoPlayer
			 */
			$xlvoPlayer = $this->voting_manager->getPlayer($this->obj_id);
			$reset_voting_id = 0;
			$xlvoPlayer->setActiveVoting($reset_voting_id);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_END_VOTING);
			$this->voting_manager->updatePlayer($xlvoPlayer);

			$this->setContentEndOfVoting();
		}
	}


	/**
	 * @param $obj_id
	 * @param $voting_id
	 *
	 * @return string
	 * @throws xlvoVotingManagerException
	 */
	public function resetVotes($obj_id, $voting_id) {
		if (!$this->access->hasWriteAccessForObject($obj_id, $this->usr->getId())) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);

			return $this->tpl->getMessageHTML($this->pl->txt('permission_denied_write'), 'failure');
		} else {
			/**
			 * @var xlvoPlayer $xlvoPlayer
			 */
			$xlvoPlayer = $this->voting_manager->getPlayer($obj_id);
			if ($xlvoPlayer->isFrozen() && !$xlvoPlayer->isUnattended() && ($xlvoPlayer->getStatus() == xlvoPlayer::STAT_RUNNING)) {
				$this->voting_manager->deleteVotesOfVoting($voting_id);
			}

			return '';
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @return string
	 */
	public function freeze($obj_id) {
		if (!$this->access->hasWriteAccessForObject($obj_id, $this->usr->getId())) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);

			return $this->tpl->getMessageHTML($this->pl->txt('permission_denied_write'), 'failure');
		} else {
			$xlvoPlayer = $this->voting_manager->getPlayer($obj_id);

			if (($xlvoPlayer->getStatus() == xlvoPlayer::STAT_RUNNING) && !$xlvoPlayer->isUnattended()) {
				$this->voting_manager->freezeVoting($obj_id);

				return '';
			} else {

				return $this->tpl->getMessageHTML($this->pl->txt('msg_voting_not_available'), 'failure');
			}
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @return string
	 */
	public function unfreeze($obj_id) {
		if (!$this->access->hasWriteAccessForObject($obj_id, $this->usr->getId())) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);

			return $this->tpl->getMessageHTML($this->pl->txt('permission_denied_write'), 'failure');
		} else {
			$xlvoPlayer = $this->voting_manager->getPlayer($obj_id);

			if (($xlvoPlayer->getStatus() == xlvoPlayer::STAT_RUNNING) && !$xlvoPlayer->isUnattended()) {
				$this->voting_manager->unfreezeVoting($obj_id);

				return '';
			} else {
				return $this->tpl->getMessageHTML($this->pl->txt('msg_voting_not_available'), 'failure');
			}
		}
	}


	/**
	 * Change Player Status of Voting to terminated and redirect to start of Voting.
	 */
	public function terminate() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
		} else {
			$this->voting_manager->terminateVoting($this->obj_id);
			$this->ctrl->redirect(new xlvoVotingGUI(), xlvoVotingGUI::CMD_STANDARD);
		}
	}


	/**
	 * Set Toolbar Content and Buttons for the Player.
	 */
	protected function initToolbar() {
		$b = xlvoLinkButton::getInstance();
		$b->clearClasses();
		$b->addCSSClass('btn-warning');
		$b->setCaption('rep_robj_xlvo_freeze');
		$b->setUrl('#');
		$b->setId('btn-freeze');
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setPrimary(true);
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

		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('voting_list'));
		$current_selection_list->setId('xlvo_select');
		$current_selection_list->setTriggerEvent('xlvo_voting');
		$current_selection_list->setUseImages(false);
		/**
		 * @var xlvoVoting[] $votings
		 */
		$votings = $this->voting_manager->getActiveVotings($this->obj_id)->get();
		foreach ($votings as $voting) {
			$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $voting->getId());
			$current_selection_list->addItem($voting->getTitle(), $voting->getId(), $this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_SHOW_VOTING));
		}
		$this->toolbar->addText($current_selection_list->getHTML());

		$b = ilLinkButton::getInstance();
		//		$b->setCaption('rep_robj_xlvo_back');
		$b->setUrl($this->ctrl->getLinkTarget($this, self::CMD_PREVIOUS));
		$b->setId('btn-previous');
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		//		$b->setCaption('rep_robj_xlvo_next');
		$b->setUrl($this->ctrl->getLinkTarget($this, self::CMD_NEXT));
		$b->setId('btn-next');
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


	/**
	 * Set GUI Content for template at the start of Voting.
	 */
	protected function setContentStartOfVoting() {
		if (!$this->voting_manager->getActiveVotings($this->obj_id)->hasSets()) {
			ilUtil::sendFailure($this->pl->txt('player_msg_no_votings'));
		} else {
			$b = ilLinkButton::getInstance();
			$b->setCaption('rep_robj_xlvo_player_start_voting');
			$b->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_VOTING));
			$b->setId('btn-start-Voting');
			$b->setPrimary(true);
			$this->toolbar->addButtonInstance($b);
		}

		$template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/tpl.player_start_screen.html', true, true);
		/**
		 * @var xlvoVotingConfig $xlvoVotingConfig
		 */
		$xlvoVotingConfig = $this->voting_manager->getVotingConfig($this->obj_id);
		$template->setVariable('PIN', $xlvoVotingConfig->getPin());

		// QR-Code implementation
		$codeContent = xlvoConf::getShortLinkURL() . $xlvoVotingConfig->getPin();

		$qrCode = new QrCode($codeContent);
		$qrCode->setSize(180);
		$qrCode->setPadding(10);
		$qrCode->setErrorCorrection('high');
		$qrCode->setForegroundColor(array(
			'r' => 0,
			'g' => 0,
			'b' => 0,
			'a' => 0
		));
		$qrCode->setBackgroundColor(array(
			'r' => 255,
			'g' => 255,
			'b' => 255,
			'a' => 0
		));
		$qrCodeData = $qrCode->getDataUri();

		$qrCodeModal = new QRCode($codeContent);
		$qrCodeModal->setSize(750);
		$qrCodeModal->setPadding(10);
		$qrCodeModal->setErrorCorrection('high');
		$qrCodeModal->setForegroundColor(array(
			'r' => 0,
			'g' => 0,
			'b' => 0,
			'a' => 0
		));
		$qrCodeModal->setBackgroundColor(array(
			'r' => 255,
			'g' => 255,
			'b' => 255,
			'a' => 0
		));
		$qrCodeDataModal = $qrCodeModal->getDataUri();

		$template->setVariable('QR-CODE', $qrCodeData);
		$template->setVariable('QR-CODE-MODAL', $qrCodeDataModal);

		$template->setVariable('SHORTLINK', xlvoConf::getShortLinkURL() . $xlvoVotingConfig->getPin());
		$template->setVariable('CLOSE_BUTTON', $this->pl->txt('cancel'));

		$this->tpl->setContent($template->get());
	}


	/**
	 * Set GUI Content for template at the end of Voting.
	 */
	protected function setContentEndOfVoting() {

		$bb = ilLinkButton::getInstance();
		$bb->setCaption('rep_robj_xlvo_back_to_voting');
		$bb->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_VOTING));
		$bb->setId('btn-back-to-Voting');

		$bt = ilLinkButton::getInstance();
		$bt->setCaption('rep_robj_xlvo_terminate');
		$bt->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_TERMINATE));
		$bt->setId('btn-terminate');

		$this->toolbar->addButtonInstance($bb);
		$this->toolbar->addButtonInstance($bt);

		$this->tpl->setContent($this->pl->txt('msg_end_of_voting'));
	}
}