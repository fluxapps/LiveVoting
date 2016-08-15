<?php
require_once("./Services/Table/classes/class.ilTable2GUI.php");
require_once("./Services/Form/classes/class.ilSelectInputGUI.php");
require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/User/class.xlvoParticipants.php");
require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/User/class.xlvoParticipant.php");
require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class xlvoResultGUI.php");

/**
 * Class xlvoResultsTableGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoResultsTableGUI extends ilTable2GUI {

	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;

	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;

	/**
	 * @var array
	 */
	protected $filter;


	public function __construct($a_parent_obj, $a_parent_cmd) {
		global $ilCtrl, $ilTabs, $ilToolbar;
		/**
		 * @var $tpl       ilTemplate
		 * @var $ilCtrl    ilCtrl
		 * @var $ilTabs    ilTabsGUI
		 * @var $ilToolbar ilToolbarGUI
		 */
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;

		$this->setId('mm_entry_list');
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setRowTemplate('tpl.results_list.html', $this->pl->getDirectory());
		$this->setTitle($this->pl->txt('results_title'));
		//
		// Columns
		$this->buildColumns();
	}

	protected function buildColumns() {
		$this->addColumn($this->pl->txt('position'), 'position', '5%');
		$this->addColumn($this->pl->txt('user'), 'user', '10%');
		$this->addColumn($this->pl->txt('question'), 'question', '35%');
		$this->addColumn($this->pl->txt('answer'), 'answer', '50%');
	}

	/**
	 * @param $obj_id int
	 */
	public function buildData($obj_id, $round_id) {
		$votingRecords = xlvoVoting::where(array("obj_id" => $obj_id));
		if($this->filter['voting'])
			$votingRecords->where(array("id" => $this->filter['voting']));
		/** @var xlvoVoting $votings */
		$votings = $votingRecords->get();
		$participants = xlvoParticipants::getInstance($obj_id)->getParticipantsForRound($round_id, $this->filter['participant']);
		$data = array();
		foreach ($participants as $participant) {
			foreach ($votings as $voting) {
				$votes = xlvoVote::where(array(
					"round_id" => $round_id,
					"voting_id" => $voting->getId(),
					"user_id" => $participant->getUserId(),
					"user_identifier" => $participant->getUserIdentifier(),
					"status" => xlvoVote::STAT_ACTIVE
				))->get();
				$data[] = array(
					"position" => $voting->getPosition(),
					"participant" => $this->getParticipantName($participant),
					"question" => $voting->getQuestion(),
					"answer" => $this->concatVotes($voting, $votes)
				);
			}
		}
		$this->setData($data);
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
		return $this->pl->txt("participant")." ".$participant->getNumber();
	}

	public function fillRow($record) {
		$this->tpl->setVariable("POSITION", $record['position']);
		$this->tpl->setVariable("USER", $record['participant']);
		$this->tpl->setVariable("QUESTION", $record['question']);
		$this->tpl->setVariable("ANSWER", $record['answer']);
	}

	/**
	 * @param $voting xlvoVoting
	 * @param $votes xlvoVote[]
	 * @return string
	 */
	private function concatVotes($voting, $votes) {
		$resultsGUI = xlvoResultGUI::getInstance($voting);
		return $resultsGUI->getTextRepresentation($votes);
	}


	public function initFilter() {
		$this->filter['participant'] = $this->getFilterItemByPostVar('participant')->getValue();
		$this->filter['voting'] = $this->getFilterItemByPostVar('voting')->getValue();
	}
}