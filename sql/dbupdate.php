<#1>
<?php
/*$fields = array(
'id' => array(
'type' => 'integer',
'length' => 4,
'notnull' => true
),
'is_online' => array(
'type' => 'integer',
'length' => 1,
'notnull' => false
),
'is_anonym' => array(
'type' => 'integer',
'length' => 1,
'notnull' => false
),
'options_type' => array(
'type' => 'integer',
'length' => 4,
'notnull' => false
),
'pin' => array(
'type' => 'text',
'length' => 10,
'notnull' => false
)
);
if(!\srag\DIC\LiveVoting\DICStatic::dic()->database()->tableExists (\LiveVoting\Option\xlvoData::TABLE_NAME)) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->createTable (\LiveVoting\Option\xlvoData::TABLE_NAME, $fields);
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->addPrimaryKey (\LiveVoting\Option\xlvoData::TABLE_NAME, array( "id" ));
}
if(!\srag\DIC\LiveVoting\DICStatic::dic()->database()->sequenceExists (\LiveVoting\Option\xlvoData::TABLE_NAME)) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->createSequence (\LiveVoting\Option\xlvoData::TABLE_NAME);
}*/
?>
<#2>
<?php
/*$fields = array(
'id' => array(
'type' => 'integer',
'length' => 4,
'notnull' => true
),
'option_id' => array(
'type' => 'integer',
'length' => 4,
'notnull' => true
),
'usr_id' => array(
'type' => 'integer',
'length' => 8,
'notnull' => false
),
'usr_session' => array(
'type' => 'text',
'length' => 100,
'notnull' => false
)
);
if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableExists (\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME)) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->createTable (\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME, $fields);
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->addPrimaryKey (\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME, array( "id" ));
}
if (!\srag\DIC\LiveVoting\DICStatic::dic()->database()->sequenceExists (\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME)) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->createSequence (\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME);
}*/
?>
<#3>
<?php
/*$fields = array(
'id' => array(
'type' => 'integer',
'length' => 4,
'notnull' => true
),
'data_id' => array(
'type' => 'integer',
'length' => 4,
'notnull' => true
),
'title' => array(
'type' => 'text',
'length' => 100,
'notnull' => false
)
);
if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableExists (\LiveVoting\Option\xlvoOptionOld::TABLE_NAME)) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->createTable (\LiveVoting\Option\xlvoOptionOld::TABLE_NAME, $fields);
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->addPrimaryKey (\LiveVoting\Option\xlvoOptionOld::TABLE_NAME, array( "id" ));
}
if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->sequenceExists (\LiveVoting\Option\xlvoOptionOld::TABLE_NAME)) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->createSequence (\LiveVoting\Option\xlvoOptionOld::TABLE_NAME);
}*/
?>
<#4>
<?php
/*if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Option\xlvoData::TABLE_NAME, 'question')) {
\srag\DIC\LiveVoting\DICStatic::dic()->database()->addTableColumn (\LiveVoting\Option\xlvoData::TABLE_NAME, 'question', array(
'type' => 'text',
'length' => 4000
));
}

if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_terminated')) {
\srag\DIC\LiveVoting\DICStatic::dic()->database()->addTableColumn (\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_terminated', array(
'type' => 'integer',
'length' => 1,
'default' => 0
));
}
if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Option\xlvoData::TABLE_NAME, 'start_time')) {
\srag\DIC\LiveVoting\DICStatic::dic()->database()->addTableColumn (\LiveVoting\Option\xlvoData::TABLE_NAME, 'start_time', array(
'type' => 'integer',
'length' => 8,
'default' => 0
));
}
if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Option\xlvoData::TABLE_NAME, 'end_time')) {
\srag\DIC\LiveVoting\DICStatic::dic()->database()->addTableColumn (\LiveVoting\Option\xlvoData::TABLE_NAME, 'end_time', array(
'type' => 'integer',
'length' => 8,
'default' => 0
));
}*/
?>
<#5>
<?php
/*$fields = array(
'lvo_key' => array(
'type' => 'text',
'length' => 64,
),
'lvo_value' => array(
'type' => 'text',
'length' => 64,
)
);
if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableExists (\LiveVoting\Conf\xlvoConfOld::TABLE_NAME)) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->createTable (\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, $fields);
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->addPrimaryKey (\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, array( "lvo_key" ));
}*/
?>
<#6>
<?php
/*if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_freezed')) {
\srag\DIC\LiveVoting\DICStatic::dic()->database()->addTableColumn (\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_freezed', array(
'type' => 'integer',
'length' => 1,
'default' => 0
));*/
?>
<#7>
<?php
/*if(\srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'lvo_key')) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->renameTableColumn (\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'lvo_key', 'config_key');
}
if(\srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'lvo_value')) {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->renameTableColumn (\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'lvo_value', 'config_value');
}
\srag\DIC\LiveVoting\DICStatic::dic()->database()->modifyTableColumn (\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'config_value', array(
'type' => 'clob',
'notnull' => false
));*/
?>
<#8>
<?php
/*if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_colorful')) {
\srag\DIC\LiveVoting\DICStatic::dic()->database()->addTableColumn (\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_colorful', array(
'type' => 'integer',
'length' => 1,
'default' => 0,
'notnull' => false
));
}*/
?>
<#9>
<?php
\LiveVoting\Option\xlvoOption::updateDB();
?>
<#10>
<?php
\LiveVoting\Vote\xlvoVote::updateDB();
?>
<#11>
<?php
\LiveVoting\Voting\xlvoVoting::updateDB();
?>
<#12>
<?php
\LiveVoting\Voting\xlvoVotingConfig::updateDB();
?>
<#13>
<?php
\LiveVoting\Player\xlvoPlayer::updateDB();
?>
<#14>
<?php
/*if (! \srag\DIC\LiveVoting\DICStatic::dic()->database()->tableColumnExists (\LiveVoting\Option\xlvoData::TABLE_NAME, 'end_time')) {
\srag\DIC\LiveVoting\DICStatic::dic()->database()->addTableColumn (\LiveVoting\Option\xlvoData::TABLE_NAME, 'end_time', array(
'type' => 'integer',
'length' => 8,
'default' => 0
));
}*/
?>
<#15>
<?php
require_once('./Services/Object/classes/class.ilObject2.php');

if (\srag\DIC\LiveVoting\DICStatic::dic()->database()->tableExists(\LiveVoting\Option\xlvoData::TABLE_NAME)) {
	$query = "SELECT * FROM " . \LiveVoting\Option\xlvoData::TABLE_NAME;
	$setData = \srag\DIC\LiveVoting\DICStatic::dic()->database()->query($query);
	while ($resData = \srag\DIC\LiveVoting\DICStatic::dic()->database()->fetchAssoc($setData)) {
		$obj_id = $resData['id'];

		/**
		 * @var $xlvoVotingConfig \LiveVoting\Voting\xlvoVotingConfig
		 */
		$xlvoVotingConfig = \LiveVoting\Voting\xlvoVotingConfig::findOrGetInstance($obj_id);
		$xlvoVotingConfig->setObjId($resData['id']);
		$xlvoVotingConfig->setObjOnline($resData['is_online']);
		$xlvoVotingConfig->setAnonymous($resData['is_anonym']);
		$xlvoVotingConfig->setTerminable($resData['is_terminated']);
		$xlvoVotingConfig->setStartDate(date(\srag\ActiveRecordConfig\LiveVoting\ActiveRecordConfig::SQL_DATE_FORMAT, $resData['start_time']));
		$xlvoVotingConfig->setEndDate(date(\srag\ActiveRecordConfig\LiveVoting\ActiveRecordConfig::SQL_DATE_FORMAT, $resData['end_time']));
		$xlvoVotingConfig->setPin($resData['pin']);
		$xlvoVotingConfig->store();

		/**
		 * @var $xlvoVoting \LiveVoting\Voting\xlvoVoting
		 */
		if (\LiveVoting\Voting\xlvoVoting::where(array( 'obj_id' => $xlvoVotingConfig->getObjId() ))->hasSets()) {
			$xlvoVoting = \LiveVoting\Voting\xlvoVoting::where(array( 'obj_id' => $xlvoVotingConfig->getObjId() ))->last();
		} else {
			$xlvoVoting = new \LiveVoting\Voting\xlvoVoting();
		}

		$xlvoVoting->setObjId($xlvoVotingConfig->getObjId());
		$xlvoVoting->setQuestion($resData['question']);
		$xlvoVoting->setColors($resData['is_colorful']);
		$xlvoVoting->setTitle(ilObject2::_lookupTitle($xlvoVotingConfig->getObjId()));
		$xlvoVoting->setMultiSelection(($resData['options_type'] == 1));
		$xlvoVoting->setVotingType(\LiveVoting\QuestionTypes\xlvoQuestionTypes::TYPE_SINGLE_VOTE);
		$xlvoVoting->setVotingStatus(\LiveVoting\Voting\xlvoVoting::STAT_ACTIVE);
		$xlvoVoting->setPosition(1);
		$xlvoVoting->store();
	}

	// rep_robj_xlvo_option
	$query = "SELECT * FROM " . \LiveVoting\Option\xlvoOptionOld::TABLE_NAME . " WHERE data_id = " . \srag\DIC\LiveVoting\DICStatic::dic()->database()
			->quote($xlvoVotingConfig->getObjId(), "integer");
	$setOption = \srag\DIC\LiveVoting\DICStatic::dic()->database()->query($query);
	while ($resOption = \srag\DIC\LiveVoting\DICStatic::dic()->database()->fetchAssoc($setOption)) {
		/**
		 * @var $xlvoOption \LiveVoting\Option\xlvoOption
		 */
		$xlvoOption = new \LiveVoting\Option\xlvoOption();
		$xlvoOption->setText($resOption['title']);
		$xlvoOption->setVotingId($xlvoVoting->getId());
		$xlvoOption->setType(\LiveVoting\QuestionTypes\xlvoQuestionTypes::TYPE_SINGLE_VOTE);
		$xlvoOption->setStatus(\LiveVoting\Option\xlvoOption::STAT_ACTIVE);
		$xlvoOption->store();

		// rep_robj_xlvo_vote
		$setVote = \srag\DIC\LiveVoting\DICStatic::dic()->database()->query("SELECT * FROM " . \LiveVoting\Vote\xlvoVoteOld::TABLE_NAME
			. " WHERE option_id = " . \srag\DIC\LiveVoting\DICStatic::dic()->database()->quote($resOption['id'], "integer"));
		while ($resVote = \srag\DIC\LiveVoting\DICStatic::dic()->database()->fetchAssoc($setVote)) {
			/**
			 * @var $xlvoVote \LiveVoting\Vote\xlvoVote
			 */
			$xlvoVote = new \LiveVoting\Vote\xlvoVote();
			$xlvoVote->setOptionId($resVote['option_id']);
			if (isset($resVote['usr_id'])) {
				$xlvoVote->setUserIdType(\LiveVoting\Vote\xlvoVote::USER_ILIAS);
				$xlvoVote->setUserId($resVote['usr_id']);
			} else {
				$xlvoVote->setUserIdType(\LiveVoting\Vote\xlvoVote::USER_ANONYMOUS);
				$xlvoVote->setUserIdentifier($resVote['usr_session']);
			}

			$xlvoVote->setType(\LiveVoting\QuestionTypes\xlvoQuestionTypes::TYPE_SINGLE_VOTE);
			$xlvoVote->setStatus(\LiveVoting\Vote\xlvoVote::STAT_ACTIVE);
			$xlvoVote->setOptionId($xlvoOption->getId());
			$xlvoVote->setVotingId($xlvoVoting->getId());
		}
	}
}

\srag\DIC\LiveVoting\DICStatic::dic()->database()->dropTable(\LiveVoting\Option\xlvoData::TABLE_NAME, false);
\srag\DIC\LiveVoting\DICStatic::dic()->database()->dropTable(\LiveVoting\Option\xlvoOptionOld::TABLE_NAME, false);
\srag\DIC\LiveVoting\DICStatic::dic()->database()->dropTable(\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME, false);
?>
<#16>
<?php
\LiveVoting\Conf\xlvoConf::updateDB();
if (\srag\DIC\LiveVoting\DICStatic::dic()->database()->tableExists(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME)) {
	$a_set = \srag\DIC\LiveVoting\DICStatic::dic()->database()->query('SELECT * FROM ' . \LiveVoting\Conf\xlvoConfOld::TABLE_NAME);
	while ($data = \srag\DIC\LiveVoting\DICStatic::dic()->database()->fetchObject($a_set)) {
		\LiveVoting\Conf\xlvoConf::set($data->config_key, $data->config_value);
	}

	\srag\DIC\LiveVoting\DICStatic::dic()->database()->dropTable(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, false);
}
?>
<#17>
<?php
\LiveVoting\Voting\xlvoVotingConfig::updateDB();
?>
<#18>
<?php
\LiveVoting\Voter\xlvoVoter::updateDB();
?>
<#19>
<?php
\LiveVoting\Player\xlvoPlayer::updateDB();
\LiveVoting\Vote\xlvoVote::updateDB();
\LiveVoting\Option\xlvoOption::updateDB();
?>
<#20>
<?php
\LiveVoting\Voting\xlvoVotingConfig::updateDB();
$xlvo_conf_table_name = \LiveVoting\Voting\xlvoVotingConfig::TABLE_NAME;
$frozen_behaviour = \LiveVoting\Voting\xlvoVotingConfig::B_FROZEN_ALWAY_OFF;
$results_behaviour = \LiveVoting\Voting\xlvoVotingConfig::B_RESULTS_ALWAY_OFF;
$q = "UPDATE {$xlvo_conf_table_name} SET frozen_behaviour={$frozen_behaviour}, results_behaviour={$results_behaviour}";
\srag\DIC\LiveVoting\DICStatic::dic()->database()->manipulate($q);
?>
<#21>
<?php
\LiveVoting\Player\xlvoPlayer::updateDB();
?>
<#22>
<?php
\LiveVoting\Voting\xlvoVoting::updateDB();
$xlvo_voting_table_name = \LiveVoting\Voting\xlvoVoting::TABLE_NAME;
$default = \LiveVoting\Voting\xlvoVoting::ROWS_DEFAULT;
$q = "UPDATE {$xlvo_voting_table_name} SET columns = {$default}";
\srag\DIC\LiveVoting\DICStatic::dic()->database()->manipulate($q);
/**
 * @var $xlvoVoting \LiveVoting\Voting\xlvoVoting
 */
foreach (\LiveVoting\Voting\xlvoVoting::get() as $xlvoVoting) {
	$xlvoVoting->regenerateOptionSorting();
}

?>
<#23>
<?php
\LiveVoting\Player\xlvoPlayer::updateDB();
?>
<#24>
<?php
\srag\DIC\LiveVoting\DICStatic::dic()->database()->manipulate("UPDATE " . \LiveVoting\Voting\xlvoVotingConfig::TABLE_NAME
	. " SET frozen_behaviour = 0, results_behaviour = 0");
?>
<#25>
<?php
\LiveVoting\Player\xlvoPlayer::updateDB();
\LiveVoting\Vote\xlvoVote::updateDB();
?>
<#26>
<?php
\LiveVoting\Round\xlvoRound::updateDB();
?>
<#27>
<?php
\LiveVoting\User\xlvoVoteHistoryObject::updateDB();
?>
<#28>
<?php
\LiveVoting\Voting\xlvoVotingConfig::updateDB();
?>
<#29>
<?php

/**
 * @var $xlvoVoting \LiveVoting\Voting\xlvoVoting
 * @var $xlvoVote   \LiveVoting\Vote\xlvoVote
 */
foreach (\LiveVoting\Voting\xlvoVoting::where(array( 'obj_id' => 0 ), '>')->get() as $xlvoVoting) {
	$list = \LiveVoting\Vote\xlvoVote::where(array(
		"round_id" => NULL,
		"voting_id" => $xlvoVoting->getId(),
	));
	if ($list->hasSets()) {
		$latestRound = \LiveVoting\Round\xlvoRound::getLatestRound($xlvoVoting->getObjId());
		foreach ($list->get() as $xlvoVote) {
			$xlvoVote->setRoundId($latestRound->getId());
			$xlvoVote->store();
		}
	}
}
?>
<#30>
<?php
\LiveVoting\Voting\xlvoVotingConfig::updateDB();
$configs = \LiveVoting\Voting\xlvoVotingConfig::get();

/**
 * @var $config \LiveVoting\Voting\xlvoVotingConfig
 */
foreach ($configs as $config) {
	$config->setShowAttendees(false);
	$config->store();
}
?>
<#31>
<?php
try {
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->addIndex(\LiveVoting\Voter\xlvoVoter::TABLE_NAME, array(
		'player_id',
		'user_identifier'
	), 'in1');
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->addIndex(\LiveVoting\Round\xlvoRound::TABLE_NAME, array( 'obj_id' ), 'in1');
	\srag\DIC\LiveVoting\DICStatic::dic()->database()->addIndex(\LiveVoting\Option\xlvoOption::TABLE_NAME, array( 'voting_id' ), 'in1');
} catch (\PDOException $ex) {

}
?>
<#32>
<?php
\LiveVoting\Conf\xlvoConf::set(\LiveVoting\Conf\xlvoConf::F_USE_GLOBAL_CACHE, 1);
?>
<#33>
<?php
\LiveVoting\Voting\xlvoVoting::updateDB();
?>
<#34>
<?php
\LiveVoting\Voting\xlvoVoting::updateDB();
?>
<#35>
<?php
\LiveVoting\Voting\xlvoVoting::updateDB();
?>
<#36>
<?php
\LiveVoting\Voting\xlvoVotingConfig::updateDB();

foreach (\LiveVoting\Voting\xlvoVotingConfig::get() as $xlvoVotingConfig) {
	/**
	 * @var \LiveVoting\Voting\xlvoVotingConfig $xlvoVotingConfig
	 */

	if (empty($xlvoVotingConfig->getPuk())) {
		$xlvoPuk = new \LiveVoting\Puk\Puk();

		$xlvoVotingConfig->setPuk($xlvoPuk->getPin());

		$xlvoVotingConfig->store();
	}
}
?>
<#37>
<?php
\LiveVoting\Voting\xlvoVotingConfig::updateDB();

foreach (\LiveVoting\Voting\xlvoVotingConfig::get() as $xlvoVotingConfig) {
	/**
	 * @var \LiveVoting\Voting\xlvoVotingConfig $xlvoVotingConfig
	 */

	$xlvoPuk = new \LiveVoting\Puk\Puk();

	if (empty($xlvoVotingConfig->getPuk()) || strlen($xlvoVotingConfig->getPuk()) < $xlvoPuk->getPinLength()) {
		$xlvoVotingConfig->setPuk($xlvoPuk->getPin());

		$xlvoVotingConfig->store();
	}
}
?>
<#38>
<?php
\LiveVoting\Voting\xlvoVoting::updateDB();

foreach (\LiveVoting\Voting\xlvoVoting::where([ "step_range" => NULL ])->get() as $voting) {
	/**
	 * @var \LiveVoting\Voting\xlvoVoting $voting
	 */
	$voting->setStepRange(\LiveVoting\QuestionTypes\NumberRange\xlvoNumberRangeSubFormGUI::STEP_RANGE_DEFAULT_VALUE);
	$voting->store();
}
?>
<#39>
<?php
\LiveVoting\Vote\xlvoVote::updateDB();

\srag\DIC\LiveVoting\DICStatic::dic()->database()->modifyTableColumn(\LiveVoting\Vote\xlvoVote::TABLE_NAME, "free_input", [
	"type" => "text",
	"length" => 2000
]);

\LiveVoting\Voting\xlvoVoting::updateDB();

foreach (\LiveVoting\Voting\xlvoVoting::where([ "answer_field" => NULL ])->get() as $voting) {
	/**
	 * @var \LiveVoting\Voting\xlvoVoting $voting
	 */
	$voting->setAnswerField(\LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputSubFormGUI::ANSWER_FIELD_SINGLE_LINE);
	$voting->store();
}
?>
<#40>
<?php
\LiveVoting\Vote\xlvoVote::updateDB();
\LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputCategory::updateDB();
?>