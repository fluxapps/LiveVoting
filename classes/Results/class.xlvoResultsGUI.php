<?php

require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Results/class.xlvoResultsTableGUI.php");
require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVoteHistoryTableGUI.php");

/**
 * Class xlvoResultsGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoResultsGUI {

	const CMD_SHOW = 'showResults';
	const CMD_NEW_ROUND = 'newRound';
	const CMD_CHANGE_ROUND = 'changeRound';
	const CMD_APPLY_FILTER = "applyFilter";
	const CMD_SHOW_HISTORY = "showHistory";
	const CMD_RESET_FILTER = 'resetFilter';
	const CMD_CONFIRM_NEW_ROUND = 'confirmNewRound';

	/**
	 * @var xlvoRound
	 */
	protected $round;



	/**
	 * @var int
	 */
	protected $obj_id = 0;

	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;

	/**
	 * @var xlvoVotingConfig
	 */
	protected $config;

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;

	public function __construct($obj_id) {
		global $ilCtrl;
		$this->obj_id = $obj_id;
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->ctrl = $ilCtrl;
		$obj_id = ilObject2::_lookupObjId($_GET['ref_id']);
		$this->config = xlvoVotingConfig::find($obj_id);
		$this->buildRound();
	}

	public function executeCommand() {
		/**
		 * @var $ilCtrl ilCtrl
		 */
		global $ilCtrl;

		$cmd = $ilCtrl->getCmd();
		switch($cmd) {
			case self::CMD_SHOW:
			case self::CMD_CHANGE_ROUND:
			case self::CMD_NEW_ROUND:
			case self::CMD_APPLY_FILTER:
			case self::CMD_RESET_FILTER:
			case self::CMD_SHOW_HISTORY:
			case self::CMD_CONFIRM_NEW_ROUND:
				$this->{$cmd}();
				break;
		}
	}

	protected function showResults() {
		global $tpl;

		$this->buildToolbar();

		$table = new xlvoResultsTableGUI($this, 'showResults', $this->config->getVotingHistory());
		$this->buildFilters($table);
		$table->initFilter();
		$table->buildData($this->obj_id, $this->round->getId());

		$tpl->setContent($table->getHTML());
	}

	private function buildRound() {
		if($_GET['round_id']) {
			$this->round = xlvoRound::find($_GET['round_id']);
		} else {
			$this->round = xlvoRound::getLatestRound($this->obj_id);
		}
	}

	protected function getRounds() {
		/** @var xlvoRound[] $rounds */
		$rounds = xlvoRound::where(array('obj_id' => $this->obj_id))->get();
		$array = array();
		foreach ($rounds as $round) {
			$array[$round->getId()] = $this->getRoundTitle($round);
		}
		return $array;
	}

	protected function getRoundTitle($round) {
		return $round->getTitle()?$round->getTitle():$this->pl->txt("common_round")." ".$round->getNumber();
	}

	protected function changeRound() {
		$round = $_POST['round_id'];
		$this->ctrl->setParameter($this, 'round_id', $round);
		$this->ctrl->redirect($this, self::CMD_SHOW);
	}

	protected function newRound() {
		$lastRound = xlvoRound::getLatestRound($this->obj_id);
		$newRound = new xlvoRound();
		$newRound->setNumber($lastRound->getNumber() + 1);
		$newRound->setObjId($this->obj_id);
		$newRound->create();
		$this->ctrl->setParameter($this, 'round_id', xlvoRound::getLatestRound($this->obj_id)->getId());
		ilUtil::sendSuccess($this->pl->txt("common_new_round_created"), true);
		$this->ctrl->redirect($this, "showResults");
	}

	protected function applyFilter() {
		$table = new xlvoResultsTableGUI($this, 'showResults');
		$this->buildFilters($table);
		$table->initFilter();
		$table->writeFilterToSession();
		$this->ctrl->redirect($this, 'showResults');
	}

	protected function resetFilter() {
		$table = new xlvoResultsTableGUI($this, 'showResults');
		$this->buildFilters($table);
		$table->initFilter();
		$table->resetFilter();
		$this->ctrl->redirect($this, 'showResults');
	}

	protected function showHistory() {
		global $tpl;

		$user_id = $_GET['user_id']?$_GET['user_id']:$_GET['user_identifier'];
		$participants = xlvoParticipants::getInstance($this->obj_id)->getParticipantsForRound($this->round->getId(), $user_id);
		/** @var xlvoParticipant $participant */
		$participant = array_shift($participants);

		$headerTpl = $this->pl->getTemplate("default/tpl.history_header.html");
		$headerTpl->setVariable("QUESTION_LABEL", $this->pl->txt("common_question"));
		$headerTpl->setVariable("QUESTION", xlvoVoting::find($_GET['voting_id'])->getQuestion());
		$headerTpl->setVariable("PARTICIPANT_LABEL", $this->pl->txt("common_participant"));
		$headerTpl->setVariable("PARTICIPANT", $this->getParticipantName($participant));
		$headerTpl->setVariable("ROUND_LABEL", $this->pl->txt("common_round"));
		$headerTpl->setVariable("ROUND", $this->getRoundTitle($this->round));

		$table = new xlvoVoteHistoryTableGUI($this, 'showHistory');
		$table->parseData($_GET['user_id'], $_GET['user_identifier'], $_GET['voting_id'], $this->round->getId());
		$tpl->setContent($headerTpl->get().$table->getHTML());
	}

	/**
	 * @param $participant xlvoParticipant
	 * @return string
	 */
	public function getParticipantName($participant) {
		if($participant->getUserIdType() == xlvoUser::TYPE_ILIAS && $participant->getUserId()) {
			$name = ilObjUser::_lookupName($participant->getUserId());
			return $name['firstname']." ".$name['lastname'];
		}
		return $this->pl->txt("common_participant")." ".$participant->getNumber();
	}

	public function confirmNewRound() {
		global $DIC;
		$tpl = $DIC['tpl'];

		$conf = new ilConfirmationGUI();
		$conf->setFormAction($this->ctrl->getFormAction($this));
		$conf->setHeaderText($this->pl->txt('common_confirm_new_round'));
		$conf->setConfirm($this->pl->txt("common_new_round"), self::CMD_NEW_ROUND);
		$conf->setCancel($this->pl->txt('common_cancel'), self::CMD_SHOW);
		$tpl->setContent($conf->getHTML());
	}

	/**
	 * @param $table xlvoResultsTableGUI
	 */
	protected function buildFilters(&$table) {
		$filter = new ilSelectInputGUI($this->pl->txt("common_participant"), "participant");
		$participants = xlvoParticipants::getInstance($this->obj_id)->getParticipantsForRound($this->round->getId());
		$options = array(0 => $this->pl->txt("common_all"));
		foreach ($participants as $participant) {
			$options[($participant->getUserIdentifier()!= null)?$participant->getUserIdentifier():$participant->getUserId()] = $this->getParticipantName($participant);
		}
		$filter->setOptions($options);
		$table->addFilterItem($filter);
		$filter->readFromSession();

		$filter = new ilSelectInputGUI($this->pl->txt("common_question"), "voting");
		$votings = array();
		$votings[0] = $this->pl->txt("common_all");
		$votings = array_merge($votings, xlvoVoting::where(array("obj_id" => $this->obj_id))->getArray("id", "question"));
		$filter->setOptions($votings);
		$table->addFilterItem($filter);
		$filter->readFromSession();
		$table->setFormAction($this->ctrl->getFormAction($this, self::CMD_APPLY_FILTER));
	}

	/**
	 *
	 */
	protected function buildToolbar() {
		global $ilToolbar;

		$button = ilLinkButton::getInstance();
		$button->setUrl($this->ctrl->getLinkTargetByClass('xlvoResultsGUI', xlvoResultsGUI::CMD_CONFIRM_NEW_ROUND));
		$button->setCaption($this->pl->txt("new_round"), false);
		$ilToolbar->addButtonInstance($button);

		$ilToolbar->addSeparator();

		$table_selection = new ilSelectInputGUI('', 'round_id');
		$options = $this->getRounds();
		$table_selection->setOptions($options);
		$table_selection->setValue($this->round->getId());

		$ilToolbar->setFormAction($this->ctrl->getFormAction($this, self::CMD_CHANGE_ROUND));
		$ilToolbar->addText($this->pl->txt("common_round"));
		$ilToolbar->addInputItem($table_selection);
		$button = ilSubmitButton::getInstance();
		$button->setCaption($this->pl->txt('common_change'), false);
		$button->setCommand(self::CMD_CHANGE_ROUND);
		$ilToolbar->addButtonInstance($button);
	}

}