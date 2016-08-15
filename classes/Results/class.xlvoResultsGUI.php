<?php

require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Results/class.xlvoResultsTableGUI.php");

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
	const CMD_RESET_FILTER = 'resetFilter';

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
	 * @var ilCtrl
	 */
	protected $ctrl;

	public function __construct($obj_id) {
		global $ilCtrl;
		$this->obj_id = $obj_id;
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->ctrl = $ilCtrl;
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
				$this->{$cmd}();
				break;
		}
	}

	protected function showResults() {
		global $tpl;

		$this->buildToolbar();

		$table = new xlvoResultsTableGUI($this, 'showResults');
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
			$array[$round->getId()] = $round->getTitle()?$round->getTitle():$this->pl->txt("round")." ".$round->getNumber();
		}
		return $array;
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

	/**
	 * @param $table xlvoResultsTableGUI
	 */
	protected function buildFilters(&$table) {
		$filter = new ilSelectInputGUI($this->pl->txt("participant"), "participant");
		$participants = xlvoParticipants::getInstance($this->obj_id)->getParticipantsForRound($this->round->getId());
		$options = array(0 => $this->pl->txt("all"));
		foreach ($participants as $participant) {
			$options[($participant->getUserIdentifier()!= null)?$participant->getUserIdentifier():$participant->getUserId()] = $table->getParticipantName($participant);
		}
		$filter->setOptions($options);
		$table->addFilterItem($filter);
		$filter->readFromSession();

		$filter = new ilSelectInputGUI($this->pl->txt("question"), "voting");
		$votings = array();
		$votings[0] = $this->pl->txt("all");
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
		$button->setUrl($this->ctrl->getLinkTargetByClass('xlvoResultsGUI', xlvoResultsGUI::CMD_NEW_ROUND));
		$button->setCaption($this->pl->txt("new_round"));
		$ilToolbar->addButtonInstance($button);

		$ilToolbar->addSeparator();

		$table_selection = new ilSelectInputGUI('', 'round_id');
		$options = $this->getRounds();
		$table_selection->setOptions($options);
		$table_selection->setValue($this->round->getId());

		$ilToolbar->setFormAction($this->ctrl->getFormAction($this, self::CMD_CHANGE_ROUND));
		$ilToolbar->addText($this->pl->txt("round"));
		$ilToolbar->addInputItem($table_selection);
		$button = ilSubmitButton::getInstance();
		$button->setCaption($this->pl->txt('change'));
		$button->setCommand(self::CMD_CHANGE_ROUND);
		$ilToolbar->addButtonInstance($button);
	}

}