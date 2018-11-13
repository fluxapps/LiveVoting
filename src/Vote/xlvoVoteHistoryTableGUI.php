<?php

namespace LiveVoting\User;

use ilDateTime;
use ilLiveVotingPlugin;
use ilTable2GUI;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoVoteHistoryTableGUI
 *
 * @package LiveVoting\Vote
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoVoteHistoryTableGUI extends ilTable2GUI {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	public function __construct($a_parent_obj, $a_parent_cmd) {
		$this->setId('xlvo_results');
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setRowTemplate('tpl.history_list.html', self::plugin()->directory());
		$this->setTitle(self::plugin()->translate('results_title'));
		//
		// Columns
		$this->buildColumns();
	}


	protected function buildColumns() {
		$this->addColumn(self::plugin()->translate('common_answer'), 'answer', '80%');
		$this->addColumn(self::plugin()->translate('common_time'), 'time', '20%');
	}


	public function parseData($user_id, $user_identifier, $voting_id, $round_id) {
		$data = xlvoVoteHistoryObject::where(array(
			"user_id" => $user_id ? $user_id : NULL,
			"user_identifier" => $user_identifier ? $user_identifier : NULL,
			"voting_id" => $voting_id,
			"round_id" => $round_id
		))->orderBy("timestamp", "DESC")->getArray(NULL, array( "answer", "timestamp" ));
		$this->setData($data);
	}


	public function fillRow($set) {
		$this->tpl->setVariable("ANSWER", $set['answer']);
		$date = new ilDateTime($set['timestamp'], IL_CAL_UNIX);
		$this->tpl->setVariable("TIMESTAMP", $date->get(IL_CAL_DATETIME));
	}
}
