<#1>
<?php
/**
 * @var $ilDB ilDB
 */
$fields = array(
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
if(!$ilDB->tableExists('rep_robj_xlvo_data')) {
	$ilDB->createTable("rep_robj_xlvo_data", $fields);
	$ilDB->addPrimaryKey("rep_robj_xlvo_data", array( "id" ));
}
if(!$ilDB->sequenceExists('rep_robj_xlvo_data')) {
	$ilDB->createSequence("rep_robj_xlvo_data");
}
?>
<#2>
<?php
$fields = array(
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
if (! $ilDB->tableExists('rep_robj_xlvo_vote')) {
	$ilDB->createTable("rep_robj_xlvo_vote", $fields);
	$ilDB->addPrimaryKey("rep_robj_xlvo_vote", array( "id" ));
}
if (!$ilDB->sequenceExists('rep_robj_xlvo_vote')) {
	$ilDB->createSequence("rep_robj_xlvo_vote");
}
?>
<#3>
<?php
$fields = array(
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
if (! $ilDB->tableExists('rep_robj_xlvo_option')) {
	$ilDB->createTable("rep_robj_xlvo_option", $fields);
	$ilDB->addPrimaryKey("rep_robj_xlvo_option", array( "id" ));
}
if (! $ilDB->sequenceExists('rep_robj_xlvo_option')) {
	$ilDB->createSequence("rep_robj_xlvo_option");
}
?>
<#4>
<?php
if (! $ilDB->tableColumnExists('rep_robj_xlvo_data', 'question')) {
$ilDB->addTableColumn('rep_robj_xlvo_data', 'question', array(
'type' => 'text',
'length' => 4000
));
}

if (! $ilDB->tableColumnExists('rep_robj_xlvo_data', 'is_terminated')) {
$ilDB->addTableColumn('rep_robj_xlvo_data', 'is_terminated', array(
'type' => 'integer',
'length' => 1,
'default' => 0
));
}
if (! $ilDB->tableColumnExists('rep_robj_xlvo_data', 'start_time')) {
$ilDB->addTableColumn('rep_robj_xlvo_data', 'start_time', array(
'type' => 'integer',
'length' => 8,
'default' => 0
));
}
if (! $ilDB->tableColumnExists('rep_robj_xlvo_data', 'end_time')) {
$ilDB->addTableColumn('rep_robj_xlvo_data', 'end_time', array(
'type' => 'integer',
'length' => 8,
'default' => 0
));
}
?>
<#5>
<?php
$fields = array(
'lvo_key' => array(
'type' => 'text',
'length' => 64,
),
'lvo_value' => array(
'type' => 'text',
'length' => 64,
)
);
if (! $ilDB->tableExists('rep_robj_xlvo_conf')) {
	$ilDB->createTable("rep_robj_xlvo_conf", $fields);
	$ilDB->addPrimaryKey("rep_robj_xlvo_conf", array( "lvo_key" ));
}
?>
<#6>
<?php
if (! $ilDB->tableColumnExists('rep_robj_xlvo_data', 'is_freezed')) {
$ilDB->addTableColumn('rep_robj_xlvo_data', 'is_freezed', array(
'type' => 'integer',
'length' => 1,
'default' => 0
));
}
?>
<#7>
<?php
if($ilDB->tableColumnExists('rep_robj_xlvo_conf', 'lvo_key')) {
	$ilDB->renameTableColumn('rep_robj_xlvo_conf', 'lvo_key', 'config_key');
}
if($ilDB->tableColumnExists('rep_robj_xlvo_conf', 'lvo_value')) {
	$ilDB->renameTableColumn('rep_robj_xlvo_conf', 'lvo_value', 'config_value');
}
$ilDB->modifyTableColumn('rep_robj_xlvo_conf', 'config_value', array(
'type' => 'clob',
'notnull' => false
));
?>
<#8>
<?php
if (! $ilDB->tableColumnExists('rep_robj_xlvo_data', 'is_colorful')) {
$ilDB->addTableColumn('rep_robj_xlvo_data', 'is_colorful', array(
'type' => 'integer',
'length' => 1,
'default' => 0,
'notnull' => false
));
}
?>
<#9>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Option/class.xlvoOption.php');
xlvoOption::installDB();
?>
<#10>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypes.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVote.php');
xlvoVote::installDB();
?>
<#11>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypes.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVoting.php');
xlvoVoting::installDB();
?>
<#12>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingConfig.php');
xlvoVotingConfig::installDB();
?>
<#13>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayer.php');
xlvoPlayer::installDB();
?>
<#14>
<?php
if (! $ilDB->tableColumnExists('rep_robj_xlvo_data', 'end_time')) {
$ilDB->addTableColumn('rep_robj_xlvo_data', 'end_time', array(
'type' => 'integer',
'length' => 8,
'default' => 0
));
}
?>
<#15>
<?php
/**
 * @var $ilDB ilDB
 */
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingConfig.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVoting.php');
require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypes.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Option/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVote.php');

$query = "SELECT * FROM rep_robj_xlvo_data";
$setData = $ilDB->query($query);
while ($resData = $ilDB->fetchAssoc($setData)) {
	$obj_id = $resData['id'];

	/**
	 * @var $xlvoVotingConfig xlvoVotingConfig
	 */
	$xlvoVotingConfig = xlvoVotingConfig::findOrGetInstance($obj_id);
	$xlvoVotingConfig->setObjId($resData['id']);
	$xlvoVotingConfig->setObjOnline($resData['is_online']);
	$xlvoVotingConfig->setAnonymous($resData['is_anonym']);
	$xlvoVotingConfig->setTerminable($resData['is_terminated']);
	$xlvoVotingConfig->setStartDate(date('Y-m-d H:i:s', $resData['start_time']));
	$xlvoVotingConfig->setEndDate(date('Y-m-d H:i:s', $resData['end_time']));
	$xlvoVotingConfig->setPin($resData['pin']);
	if (!xlvoVotingConfig::where(array( 'obj_id' => $xlvoVotingConfig->getObjId() ))->hasSets()) {
		$xlvoVotingConfig->create();
	} else {
		$xlvoVotingConfig->update();
	}

	/**
	 * @var $xlvoVoting xlvoVoting
	 */
	if (xlvoVoting::where(array( 'obj_id' => $xlvoVotingConfig->getObjId() ))->hasSets()) {
		$xlvoVoting = xlvoVoting::where(array( 'obj_id' => $xlvoVotingConfig->getObjId() ))->last();
	} else {
		$xlvoVoting = new xlvoVoting();
	}

	$xlvoVoting->setObjId($xlvoVotingConfig->getObjId());
	$xlvoVoting->setQuestion($resData['question']);
	$xlvoVoting->setColors($resData['is_colorful']);
	$xlvoVoting->setTitle(ilObject2::_lookupTitle($xlvoVotingConfig->getObjId()));
	$xlvoVoting->setMultiSelection(($resData['options_type'] == 1));
	$xlvoVoting->setVotingType(xlvoQuestionTypes::TYPE_SINGLE_VOTE);
	$xlvoVoting->setVotingStatus(xlvoVoting::STAT_ACTIVE);
	$xlvoVoting->setPosition(1);
	if ($xlvoVoting->getId()) {
		$xlvoVoting->update();
	} else {
		$xlvoVoting->create();
	}

	// rep_robj_xlvo_option
	$query = "SELECT * FROM rep_robj_xlvo_option WHERE data_id = " . $ilDB->quote($xlvoVotingConfig->getObjId(), "integer");
	$setOption = $ilDB->query($query);
	while ($resOption = $ilDB->fetchAssoc($setOption)) {
		/**
		 * @var $xlvoOption xlvoOption
		 */
		$xlvoOption = new xlvoOption();
		$xlvoOption->setText($resOption['title']);
		$xlvoOption->setVotingId($xlvoVoting->getId());
		$xlvoOption->setType(xlvoQuestionTypes::TYPE_SINGLE_VOTE);
		$xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
		$xlvoOption->create();

		// rep_robj_xlvo_vote
		$setVote = $ilDB->query("SELECT * FROM rep_robj_xlvo_vote " . " WHERE option_id = " . $ilDB->quote($resOption['id'], "integer"));
		while ($resVote = $ilDB->fetchAssoc($setVote)) {
			/**
			 * @var $xlvoVote xlvoVote
			 */
			$xlvoVote = new xlvoVote();
			$xlvoVote->setOptionId($resVote['option_id']);
			if (isset($resVote['usr_id'])) {
				$xlvoVote->setUserIdType(xlvoVote::USER_ILIAS);
				$xlvoVote->setUserId($resVote['usr_id']);
			} else {
				$xlvoVote->setUserIdType(xlvoVote::USER_ANONYMOUS);
				$xlvoVote->setUserIdentifier($resVote['usr_session']);
			}

			$xlvoVote->setType(xlvoQuestionTypes::TYPE_SINGLE_VOTE);
			$xlvoVote->setStatus(xlvoVote::STAT_ACTIVE);
			$xlvoVote->setOptionId($xlvoOption->getId());
			$xlvoVote->setVotingId($xlvoVoting->getId());
		}
	}
}
?>
<#16>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConf.php');
xlvoConf::installDB();
$a_set = $ilDB->query('SELECT * FROM rep_robj_xlvo_conf');
while ($data = $ilDB->fetchObject($a_set)) {
	xlvoConf::set($data->config_key, $data->config_value);
}
?>
<#17>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingConfig.php');
xlvoVotingConfig::updateDB();
?>
<#18>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voter/class.xlvoVoter.php');
xlvoVoter::installDB();
?>
<#19>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayer.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVote.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Option/class.xlvoOption.php');
xlvoPlayer::updateDB();
xlvoVote::updateDB();
xlvoOption::updateDB();
?>
<#20>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingConfig.php');
xlvoVotingConfig::updateDB();
$xlvo_conf_table_name = xlvoVotingConfig::returnDbTableName();
$frozen_behaviour = xlvoVotingConfig::B_FROZEN_ALWAY_OFF;
$results_behaviour = xlvoVotingConfig::B_RESULTS_ALWAY_OFF;
$q = "UPDATE {$xlvo_conf_table_name} SET frozen_behaviour={$frozen_behaviour}, results_behaviour={$results_behaviour}";
$ilDB->manipulate($q);
?>
<#21>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayer.php');
xlvoPlayer::updateDB();
?>
<#22>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVoting.php');
xlvoVoting::updateDB();
$xlvo_voting_table_name = xlvoVoting::returnDbTableName();
$default = xlvoVoting::ROWS_DEFAULT;
$q = "UPDATE {$xlvo_voting_table_name} SET columns = {$default}";
$ilDB->manipulate($q);
/**
 * @var $xlvoVoting xlvoVoting
 */
foreach (xlvoVoting::get() as $xlvoVoting) {
	$xlvoVoting->renegerateOptionSorting();
}

?>
<#23>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayer.php');
xlvoPlayer::updateDB();
?>
<#24>
<?php
$ilDB->manipulate("UPDATE rep_robj_xlvo_config_n SET frozen_behaviour = 0, results_behaviour = 0");
?>
<#25>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayer.php');
xlvoPlayer::updateDB();

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVote.php');
xlvoVote::updateDB();
?>
<#26>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Round/class.xlvoRound.php');
xlvoRound::installDB();
?>
<#27>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVoteHistoryObject.php');
xlvoVoteHistoryObject::installDB();
?>
<#28>
<?php
require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingConfig.php");
xlvoVotingConfig::updateDB();
?>