<?php

use LiveVoting\User\xlvoParticipant;
use LiveVoting\User\xlvoParticipants;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;

require_once("./Services/Table/classes/class.ilTable2GUI.php");
require_once("./Services/Form/classes/class.ilSelectInputGUI.php");

/**
 * Class xlvoResultsTableGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoResultsTableGUI extends ilTable2GUI {

	const LENGTH = 100;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;
	/**
	 * @var \ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var \ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var array
	 */
	protected $filter;
	/**
	 * @var bool
	 */
	protected $showHistory = false;
	/**
	 * @var xlvoResultsGUI
	 */
	protected $parent_obj;


	/**
	 * xlvoResultsTableGUI constructor.
	 *
	 * @param $a_parent_obj
	 * @param string $a_parent_cmd
	 * @param bool $show_history
	 */
	public function __construct(xlvoResultsGUI $a_parent_obj, $a_parent_cmd, $show_history = false) {
		global $ilCtrl, $ilTabs;
		/**
		 * @var $tpl       \ilTemplate
		 * @var $ilCtrl    \ilCtrl
		 * @var $ilTabs    \ilTabsGUI
		 */
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;

		$this->setId('xlvo_results');
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setRowTemplate('tpl.results_list.html', $this->pl->getDirectory());
		$this->setTitle($this->pl->txt('results_title'));
		$this->showHistory = $show_history;
		$this->setExportFormats(array( self::EXPORT_CSV ));
		//
		// Columns
		$this->buildColumns();
	}


	protected function buildColumns() {
		$this->addColumn($this->pl->txt('common_position'), 'position', '5%');
		$this->addColumn($this->pl->txt('common_user'), 'user', '10%');
		$this->addColumn($this->pl->txt('voting_title'), 'title', '10%');
		$this->addColumn($this->pl->txt('common_question'), 'question', '35%');
		$this->addColumn($this->pl->txt('common_answer'), 'answer', '40%');
		if ($this->isShowHistory()) {
			$this->addColumn($this->pl->txt('common_history'), "", 'auto');
		}
	}


	/**
	 * @param $obj_id
	 * @param $round_id
	 */
	public function buildData($obj_id, $round_id) {
		$votingRecords = xlvoVoting::where(array( "obj_id" => $obj_id ));
		if ($this->filter['voting']) {
			$votingRecords->where(array( "id" => $this->filter['voting'] ));
		}
		if ($this->filter['voting_title']) {
			$votingRecords->where(array( "id" => $this->filter['voting_title'] ));
		}
		/**
		 * @var $votings      xlvoVoting[]
		 * @var $participants xlvoParticipant[]
		 */
		$votings = $votingRecords->get();
		$participants = xlvoParticipants::getInstance($obj_id)->getParticipantsForRound($round_id, $this->filter['participant']);
		$data = array();
		foreach ($participants as $participant) {
			foreach ($votings as $voting) {
				$votes = xlvoVote::where(array(
					"round_id"        => $round_id,
					"voting_id"       => $voting->getId(),
					"user_id"         => $participant->getUserId(),
					"user_identifier" => $participant->getUserIdentifier(),
					"status"          => xlvoVote::STAT_ACTIVE,
				))->get();
				$data[] = array(
					"position"        => $voting->getPosition(),
					"participant"     => $this->parent_obj->getParticipantName($participant),
					"user_id"         => $participant->getUserId(),
					"user_identifier" => $participant->getUserIdentifier(),
					"title"           => $voting->getTitle(),
					"question"        => strip_tags($voting->getQuestionForPresentation()),
					"answer"          => $this->concatVotes($voting, $votes),
					"voting_id"       => $voting->getId(),
					"round_id"        => $round_id,
				);
			}
		}
		$this->setData($data);
	}


	/**
	 * @param array $record
	 */
	public function fillRow($record) {
		$this->tpl->setVariable("POSITION", $record['position']);
		$this->tpl->setVariable("USER", $record['participant']);
		$this->tpl->setVariable("QUESTION", $this->shorten($record['question']));
		$this->tpl->setVariable("TITLE", $this->shorten($record['title']));
		$this->tpl->setVariable("ANSWER", $this->shorten($record['answer']));
		if ($this->isShowHistory()) {
			$this->tpl->setVariable("ACTION", $this->pl->txt("common_show_history"));
			$this->ctrl->setParameter($this->parent_obj, 'round_id', $record['round_id']);
			$this->ctrl->setParameter($this->parent_obj, 'user_id', $record['user_id']);
			$this->ctrl->setParameter($this->parent_obj, 'user_identifier', $record['user_identifier']);
			$this->ctrl->setParameter($this->parent_obj, 'voting_id', $record['voting_id']);
			$this->tpl->setVariable("ACTION_URL", $this->ctrl->getLinkTarget($this->parent_obj, 'showHistory'));
		}
	}


	/**
	 * @param $voting xlvoVoting
	 * @param $votes  xlvoVote[]
	 * @return string
	 */
	private function concatVotes($voting, $votes) {
		$resultsGUI = xlvoResultGUI::getInstance($voting);

		return $resultsGUI->getTextRepresentation($votes);
	}


	public function initFilter() {
		$this->filter['participant'] = $this->getFilterItemByPostVar('participant')->getValue();
		$this->filter['voting'] = $this->getFilterItemByPostVar('voting')->getValue();
		$this->filter['voting_title'] = $this->getFilterItemByPostVar('voting_title')->getValue();
	}


	/**
	 * @return boolean
	 */
	public function isShowHistory() {
		return $this->showHistory;
	}


	/**
	 * @param boolean $showHistory
	 */
	public function setShowHistory($showHistory) {
		$this->showHistory = $showHistory;
	}


	/**
	 * @param object $a_csv
	 * @return null
	 */
	protected function fillHeaderCSV($a_csv) {
		return null;
	}


	/**
	 * @return array
	 */
	protected function getCSVCols() {
		return array( 'participant' => 'participant', 'title' => 'title', 'question' => 'question', 'answer' => 'answer' );
	}


	/**
	 * @param object $a_csv
	 * @param array $a_set
	 */
	protected function fillRowCSV($a_csv, $a_set) {
		$a_set = array_intersect_key($a_set, $this->getCSVCols());
		array_walk($a_set, function (&$value) {
			//			$value = mb_convert_encoding($value, 'ISO-8859-1');
			//			$value = mb_convert_encoding($value, "UTF-8", "UTF-8");
			//			$value = utf8_encode($value);
			//			$value = iconv('UTF-8', 'macintosh', $value);
		});
		parent::fillRowCSV($a_csv, $a_set);
	}


	/**
	 * @param string $question
	 * @return string
	 */
	protected function shorten($question) {
		return strlen($question) > self::LENGTH ? substr($question, 0, self::LENGTH) . "..." : $question;
	}
}