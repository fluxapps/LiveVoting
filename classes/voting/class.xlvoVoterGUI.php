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

/**
 * @ilCtrl_isCalledBy xlvoVoterGUI: ilUIPluginRouterGUI
 */
class xlvoVoterGUI {

	const TAB_STANDARD = 'tab_voter';
	const CMD_STANDARD = 'showVoting';
	const CMD_ADD = 'add';
	const CMD_CREATE = 'create';
	const CMD_EDIT = 'edit';
	const CMD_UPDATE = 'update';
	const CMD_CANCEL = 'cancel';
	const CMD_ACCESS_VOTING = 'accessVoting';
	const CMD_WAITING_SCREEN = 'waitingScreen';
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
	 * @var ilUser
	 */
	protected $usr;
	/**
	 * @var xlvoVoting_manager
	 */
	protected $voting_manager;


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilUser, $ilToolbar;

		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/display_voter.js');
		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/vote_singlevote.js');
		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/vote_freeinput.js');
		$tpl->addJavascript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/multi_line_input.js');

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
						$this->generateAnonymousSession();
					}

					return $this->showInfoScreen($config->getObjId(), self::INFO_TYPE_WAITING);
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

		// TODO check access

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
			$vote = new xlvoVote();
			$vote->setStatus(xlvoVote::STAT_INACTIVE);
			$vote->setVotingId(0);
			$vote->setOptionId($vote->getOptionId());

			return $vote;
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
		$template->setVariable('OBJ_ID', $obj_id);
		$template->setVariable('INFO_TYPE', $info_type);
		$template->setVariable('INFO_TEXT', $this->pl->txt('msg_' . $info_type) . 'session_id: ' . $_SESSION['user_identifier']);

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

		$template->setVariable('INFO_TEXT', $this->pl->txt('msg_access_screen') . $form->getHTML());

		if ($error_msg) {
			$template->setVariable('ERROR', $this->pl->txt('msg_validation_error_pin'));
		}

		return $template->get();
	}


	protected function generateAnonymousSession() {

		if (empty($_SESSION['user_identifier'])) {
			session_start();

			$new_id = false;

			while (! $new_id) {
				$user_identifier = rand(1, 100000);
				$existing = xlvoVote::where(array( 'user_identifier' => $user_identifier ))->count();
				if ($existing <= 0) {
					$new_id = true;
				}
			}

			if (isset($user_identifier)) {
				$_SESSION['user_identifier'] = $user_identifier;
			}
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