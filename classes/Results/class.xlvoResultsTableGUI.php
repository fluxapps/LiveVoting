<?php

use LiveVoting\User\xlvoParticipant;
use LiveVoting\User\xlvoParticipants;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Results\xlvoResults;

require_once("./Services/Table/classes/class.ilTable2GUI.php");
require_once("./Services/Form/classes/class.ilSelectInputGUI.php");

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
		global $DIC;
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();

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
		$this->addColumn($this->pl->txt('common_position'), 'position', '1%');
		$this->addColumn($this->pl->txt('common_user'), 'user', '10%');
		$this->addColumn($this->pl->txt('voting_title'), 'title', '15%');
		$this->addColumn($this->pl->txt('common_question'), 'question', '20%');
		$this->addColumn($this->pl->txt('common_answer'), 'answer', 'auto');
		if ($this->isShowHistory()) {
			$this->addColumn($this->pl->txt('common_history'), "", 'auto');
		}
	}


	/**
	 * @param $obj_id
	 * @param $round_id
	 */
	public function buildData($obj_id, $round_id) {
		$xlvoResults = new xlvoResults($obj_id, $round_id);

		$a_data = $xlvoResults->getData($this->filter, $this->parent_obj->getParticipantNameCallable(), function ($voting, $votes) {
			$resultsGUI = xlvoResultGUI::getInstance($voting);

			return $resultsGUI->getTextRepresentation($votes);
		});

		$this->setData($a_data);
	}


	/**
	 * @param array $record
	 */
	public function fillRow($record) {
		$this->tpl->setVariable("POSITION", $record['position']);
		$this->tpl->setVariable("USER", $record['participant']);
		$this->tpl->setVariable("QUESTION", $this->shorten($record['question'], 40));
		$this->tpl->setVariable("TITLE", $this->shorten($record['title'], 40));
		$this->tpl->setVariable("ANSWER", $this->shorten($record['answer'], 100));
		if ($this->isShowHistory()) {
			$this->tpl->setVariable("ACTION", $this->pl->txt("common_show_history"));
			$this->ctrl->setParameter($this->parent_obj, 'round_id', $record['round_id']);
			$this->ctrl->setParameter($this->parent_obj, 'user_id', $record['user_id']);
			$this->ctrl->setParameter($this->parent_obj, 'user_identifier', $record['user_identifier']);
			$this->ctrl->setParameter($this->parent_obj, 'voting_id', $record['voting_id']);
			$this->tpl->setVariable("ACTION_URL", $this->ctrl->getLinkTarget($this->parent_obj, xlvoResultsGUI::CMD_SHOW_HISTORY));
		}
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
		return array(
			'participant' => 'participant',
			'title'       => 'title',
			'question'    => 'question',
			'answer'      => 'answer',
		);
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
	 * @param $question
	 * @param int $length
	 * @return string
	 */
	protected function shorten($question, $length = xlvoResultsGUI::LENGTH) {
		$closure = $this->parent_obj->getShortener($length);

		return $closure($question);
	}
}