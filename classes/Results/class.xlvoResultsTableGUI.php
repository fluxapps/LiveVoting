<?php
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
	 * @var ilCtrl
	 */
	protected $ctrl;

	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;

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
		$this->addColumn($this->pl->txt('user'), 'user', 'auto');
		$this->addColumn($this->pl->txt('question'), 'question', 'auto');
		$this->addColumn($this->pl->txt('answer'), 'answer', 'auto');
	}

	/**
	 * @param $obj_id int
	 */
	public function buildData($obj_id, $round_id) {
		/** @var xlvoVoting[] $votings */
		$votings = xlvoVoting::where(array("obj_id" => $obj_id))->get();
		$users = xlvoUsers::getInstance($obj_id)->getUsersForRound($round_id);
		$rows = array();
		foreach ($users as $user) {
			foreach ($votings as $voting) {
				
			}
		}
	}


}