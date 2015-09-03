<?php
require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoDisplayVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoMultiLineInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoInitialisation.php');

/**
 * @ilCtrl_isCalledBy xlvoVoterGUI: ilUIPluginRouterGUI
 */
class xlvoVoterGUI {

	const TAB_STANDARD = 'tab_voter';
	const CMD_STANDARD = 'showVoting';
	const CMD_ACCESS_VOTING = 'accessVoting';
	const CMD_SHOW_ACCESS_SCREEN = 'showAccessScreen';
	const TPL_INFO_SCREEN = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.info_screen_voter.html';
	const INFO_TYPE_WAITING = 'waiting_screen';
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
	 * @var xlvoVoting_manager
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

		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/display_voter.js');
		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/vote_singlevote.js');
		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/vote_freeinput.js');
		$tpl->addJavascript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/multi_line_input.js');
	}


	public function executeCommand() {

		$this->tabs->addTab(self::TAB_STANDARD, $this->pl->txt('voting'), $this->ctrl->getLinkTarget($this, self::CMD_STANDARD));
		$this->tabs->setTabActive(self::TAB_STANDARD);
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
		$this->tpl->getStandardTemplate();
		$this->tpl->setVariable('BASE', '/');
		$this->tpl->show();
	}


	/**
	 * @param null $voting_id
	 *
	 * @return string
	 */
	public function showVoting($obj_id = NULL, $voting_id = NULL) {
		if ($obj_id == NULL) {
			$obj_id = 0;
			$this->tpl->setContent($this->showInfoScreen($obj_id, self::INFO_TYPE_WAITING));

			return $this->showInfoScreen($obj_id, self::INFO_TYPE_WAITING);
		} else {
			$xlvoPlayer = $this->voting_manager->getPlayer($obj_id);

			if ($xlvoPlayer instanceof xlvoPlayer) {

				if ($voting_id != $xlvoPlayer->getActiveVoting()) {

					$xlvoVoting = $this->voting_manager->getVoting($xlvoPlayer->getActiveVoting());

					if ($xlvoVoting instanceof xlvoVoting) {
						$display = new xlvoDisplayVoterGUI($xlvoVoting);

						return $display->getHtml();
					} else {
						return $this->showInfoScreen($obj_id, self::INFO_TYPE_WAITING);
					}
				} else {
					return '';
				}
			} else {
				return $this->showAccessScreen(true);
			}
		}
	}


	public function accessVoting($pin) {
		if ($pin == NULL) {
			return $this->showAccessScreen(true);
		} else {
			$config = $this->voting_manager->getVotingConfigs()->where(array( 'pin' => $pin ))->first();
			if ($config instanceof xlvoVotingConfig) {
				if ($pin == $config->getPin()) {
					if ($config->isAnonymous()) {
						//						$this->generateAnonymousSession();
					} else {
						xlvoInitialisation::init(xlvoInitialisation::CONTEXT_ILIAS);
					}

					return $this->showInfoScreen($config->getObjId(), self::INFO_TYPE_WAITING) . session_id();
				} else {
					return $this->showAccessScreen(true);
				}
			} else {
				return $this->showAccessScreen(true);
			}
		}
	}


	/**
	 * @param xlvoVote $vote
	 *
	 * @return xlvoVote
	 */
	public function vote(xlvoVote $vote) {
		$option = $this->voting_manager->getOption($vote->getOptionId());
		$voting = $this->voting_manager->getVoting($option->getVotingId());
		$obj_id = $voting->getObjId();
		if ($this->checkVotingAccess($obj_id)) {
			$xlvoVote = new xlvoVote();
			$xlvoVote->setOptionId($vote->getOptionId());
			$xlvoVote->setId($vote->getId());
			$xlvoVote->setStatus($vote->getStatus());
			$xlvoVote->setFreeInput($vote->getFreeInput());

			$vote = $this->voting_manager->vote($xlvoVote);

			if ($vote instanceof xlvoVote) {
				return $vote;
			} else {
				// TODO implement exception
				$new_vote = new xlvoVote();
				$new_vote->setStatus(xlvoVote::STAT_INACTIVE);
				$new_vote->setVotingId(0);

				//				$new_vote->setOptionId($vote->getOptionId());

				return $vote;
			}
		} else {
			$this->ctrl->redirect(new xlvoVoterGUI(), self::CMD_SHOW_ACCESS_SCREEN);
		}
	}


	public function getVotingData($obj_id) {
		if ($obj_id == NULL || $obj_id == 0) {
			$data = array(
				'voIsFrozen' => 0,
				'voIsReset' => 0,
				'voStatus' => xlvoPlayer::STAT_STOPPED,
				'voHasAccess' => 0,
				'voIsAvailable' => 1
			);

			return $data;
		} else {
			$config = $this->voting_manager->getVotingConfig($obj_id);
			$player = $this->voting_manager->getPlayer($obj_id);
			$data = array(
				'voIsFrozen' => $config->isFrozen(),
				'voIsReset' => $player->isReset(),
				'voStatus' => $player->getStatus(),
				'voHasAccess' => $this->checkVotingAccess($obj_id),
				'voIsAvailable' => $this->voting_manager->isVotingAvailable($obj_id)
			);

			return $data;
		}
	}


	public function showInfoScreen($obj_id, $info_type) {
		$template = new ilTemplate(self::TPL_INFO_SCREEN, true, true);
		$template->setVariable('VOTING_ID', 0);
		$template->touchBlock('loader');
		$template->setVariable('OBJ_ID', $obj_id);
		$template->setVariable('INFO_TYPE', $info_type);
		$template->setVariable('INFO_TEXT', $this->pl->txt('msg_' . $info_type));

		return $template->get();
	}


	public function showAccessScreen($error_msg = false) {

		$template = new ilTemplate(self::TPL_INFO_SCREEN, true, true);
		$template->setVariable('VOTING_ID', 0);
		$template->setVariable('OBJ_ID', 0);
		$template->setVariable('INFO_TYPE', 'access_screen');

		$t = new ilTextInputGUI($this->pl->txt('pin_input'), 'pin_input');
		$form = new ilPropertyFormGUI();
		$form->setId('access');
		$form->addItem($t);
		$form->addCommandButton(self::CMD_ACCESS_VOTING, $this->pl->txt('send'));

		$template->setVariable('INFO_TEXT', $this->pl->txt('msg_access_screen') . ' --- user: ' . $this->usr->getId() . ' user_i: ' . session_id()
			. ' context: ' . $_COOKIE['xlvo_context'] . $form->getHTML());

		if ($error_msg) {
			$template->setVariable('ERROR', $this->pl->txt('msg_validation_error_pin'));
		}

		return $template->get();
	}


	protected function generateAnonymousSession() {
		$session_id = session_id();
		if (! $session_id) {
			xlvoInitialisation::init(xlvoInitialisation::CONTEXT_PIN);
		}
	}


	public function checkVotingAccess($obj_id) {
		$config = $this->voting_manager->getVotingConfig($obj_id);
		if ($config->isAnonymous()) {
			return true;
		} elseif ($this->usr->getId() > 0) {
			// TODO has read access
			return true;
		} else {
			return false;
		}
	}
}