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
				$this->{$cmd}();
				break;
		}
	}

	protected function showResults() {
		global $tpl;

		$this->buildToolbar();
		$table = $this->buildTable();

		$tpl->setContent($table->getHTML());
	}

	private function buildRound() {
		if($_GET['results_id']) {
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

	/**
	 * @return xlvoResultsTableGUI
	 */
	protected function buildTable() {
		$table = new xlvoResultsTableGUI($this, 'showResults');
		$table->buildData($this->obj_id, $this->round->getId());

		return $table;
	}
}