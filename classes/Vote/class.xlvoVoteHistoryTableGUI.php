<?php

require_once("./Services/Table/classes/class.ilTable2GUI.php");

/**
 * Class xlvoVoteHistoryTableGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoVoteHistoryTableGUI extends ilTable2GUI {

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

	public function __construct($a_parent_obj, $a_parent_cmd) {
		global $ilCtrl, $ilTabs;
		/**
		 * @var $tpl       ilTemplate
		 * @var $ilCtrl    ilCtrl
		 * @var $ilTabs    ilTabsGUI
		 * @var $ilToolbar ilToolbarGUI
		 */
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;

		$this->setId('xlvo_results');
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setRowTemplate('tpl.history_list.html', $this->pl->getDirectory());
		$this->setTitle($this->pl->txt('results_title'));
		//
		// Columns
		$this->buildColumns();
	}

	protected function buildColumns() {
		$this->addColumn($this->pl->txt('common_answer'), 'answer', '80%');
		$this->addColumn($this->pl->txt('common_time'), 'time', '20%');
	}

	public function parseData($user_id, $user_identifier, $voting_id, $round_id) {
		$data = xlvoVoteHistoryObject::where(array(
			"user_id" => $user_id?$user_id:null,
			"user_identifier" => $user_identifier?$user_identifier:null,
			"voting_id" => $voting_id,
			"round_id" => $round_id
		))->orderBy("timestamp", "DESC")->getArray(null, array("answer", "timestamp"));
		$this->setData($data);
	}

	public function fillRow($set) {
		$this->tpl->setVariable("ANSWER", $set['answer']);
		$date = new ilDateTime($set['timestamp'], IL_CAL_UNIX);
		$this->tpl->setVariable("TIMESTAMP", $date->get(IL_CAL_DATETIME));
	}
}