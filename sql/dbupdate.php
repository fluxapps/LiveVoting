<#1>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
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
if(!$ilDB->tableExists(\LiveVoting\Option\xlvoData::TABLE_NAME)) {
	$ilDB->createTable(\LiveVoting\Option\xlvoData::TABLE_NAME, $fields);
	$ilDB->addPrimaryKey(\LiveVoting\Option\xlvoData::TABLE_NAME, array( "id" ));
}
if(!$ilDB->sequenceExists(\LiveVoting\Option\xlvoData::TABLE_NAME)) {
	$ilDB->createSequence(\LiveVoting\Option\xlvoData::TABLE_NAME);
}
?>
<#2>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
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
if (! $ilDB->tableExists(\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME)) {
	$ilDB->createTable(\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME, $fields);
	$ilDB->addPrimaryKey(\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME, array( "id" ));
}
if (!$ilDB->sequenceExists(\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME)) {
	$ilDB->createSequence(\LiveVoting\Vote\xlvoVoteOld::TABLE_NAME);
}
?>
<#3>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
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
if (! $ilDB->tableExists(\LiveVoting\Option\xlvoOptionOld::TABLE_NAME)) {
	$ilDB->createTable(\LiveVoting\Option\xlvoOptionOld::TABLE_NAME, $fields);
	$ilDB->addPrimaryKey(\LiveVoting\Option\xlvoOptionOld::TABLE_NAME, array( "id" ));
}
if (! $ilDB->sequenceExists(\LiveVoting\Option\xlvoOptionOld::TABLE_NAME)) {
	$ilDB->createSequence(\LiveVoting\Option\xlvoOptionOld::TABLE_NAME);
}
?>
<#4>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
if (! $ilDB->tableColumnExists(\LiveVoting\Option\xlvoData::TABLE_NAME, 'question')) {
$ilDB->addTableColumn(\LiveVoting\Option\xlvoData::TABLE_NAME, 'question', array(
'type' => 'text',
'length' => 4000
));
}

if (! $ilDB->tableColumnExists(\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_terminated')) {
$ilDB->addTableColumn(\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_terminated', array(
'type' => 'integer',
'length' => 1,
'default' => 0
));
}
if (! $ilDB->tableColumnExists(\LiveVoting\Option\xlvoData::TABLE_NAME, 'start_time')) {
$ilDB->addTableColumn(\LiveVoting\Option\xlvoData::TABLE_NAME, 'start_time', array(
'type' => 'integer',
'length' => 8,
'default' => 0
));
}
if (! $ilDB->tableColumnExists(\LiveVoting\Option\xlvoData::TABLE_NAME, 'end_time')) {
$ilDB->addTableColumn(\LiveVoting\Option\xlvoData::TABLE_NAME, 'end_time', array(
'type' => 'integer',
'length' => 8,
'default' => 0
));
}
?>
<#5>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
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
if (! $ilDB->tableExists(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME)) {
	$ilDB->createTable(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, $fields);
	$ilDB->addPrimaryKey(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, array( "lvo_key" ));
}
?>
<#6>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
if (! $ilDB->tableColumnExists(\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_freezed')) {
$ilDB->addTableColumn(\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_freezed', array(
'type' => 'integer',
'length' => 1,
'default' => 0
));
}
?>
<#7>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
if($ilDB->tableColumnExists(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'lvo_key')) {
	$ilDB->renameTableColumn(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'lvo_key', 'config_key');
}
if($ilDB->tableColumnExists(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'lvo_value')) {
	$ilDB->renameTableColumn(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'lvo_value', 'config_value');
}
$ilDB->modifyTableColumn(\LiveVoting\Conf\xlvoConfOld::TABLE_NAME, 'config_value', array(
'type' => 'clob',
'notnull' => false
));
?>
<#8>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
if (! $ilDB->tableColumnExists(\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_colorful')) {
$ilDB->addTableColumn(\LiveVoting\Option\xlvoData::TABLE_NAME, 'is_colorful', array(
'type' => 'integer',
'length' => 1,
'default' => 0,
'notnull' => false
));
}
?>
<#9>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Option\xlvoOption::updateDB();
?>
<#10>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Vote\xlvoVote::updateDB();
?>
<#11>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Voting\xlvoVoting::updateDB();
?>
<#12>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

xlvoVotingConfig::updateDB();
?>
<#13>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Player\xlvoPlayer::updateDB();
?>
<#14>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
if (! $ilDB->tableColumnExists(\LiveVoting\Option\xlvoData::TABLE_NAME, 'end_time')) {
$ilDB->addTableColumn(\LiveVoting\Option\xlvoData::TABLE_NAME, 'end_time', array(
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
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
require_once('./Services/Object/classes/class.ilObject2.php');

$query = "SELECT * FROM " . \LiveVoting\Option\xlvoData::TABLE_NAME;
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
	if ($xlvoVoting->getId()) {
		$xlvoVoting->update();
	} else {
		$xlvoVoting->create();
	}

	// rep_robj_xlvo_option
	$query = "SELECT * FROM " . \LiveVoting\Option\xlvoOptionOld::TABLE_NAME . " WHERE data_id = " . $ilDB->quote($xlvoVotingConfig->getObjId(), "integer");
	$setOption = $ilDB->query($query);
	while ($resOption = $ilDB->fetchAssoc($setOption)) {
		/**
		 * @var $xlvoOption \LiveVoting\Option\xlvoOption
		 */
		$xlvoOption = new \LiveVoting\Option\xlvoOption();
		$xlvoOption->setText($resOption['title']);
		$xlvoOption->setVotingId($xlvoVoting->getId());
		$xlvoOption->setType(\LiveVoting\QuestionTypes\xlvoQuestionTypes::TYPE_SINGLE_VOTE);
		$xlvoOption->setStatus(\LiveVoting\Option\xlvoOption::STAT_ACTIVE);
		$xlvoOption->create();

		// rep_robj_xlvo_vote
		$setVote = $ilDB->query("SELECT * FROM " . \LiveVoting\Vote\xlvoVoteOld::TABLE_NAME . " WHERE option_id = " . $ilDB->quote($resOption['id'], "integer"));
		while ($resVote = $ilDB->fetchAssoc($setVote)) {
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
?>
<#16>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Conf\xlvoConf::updateDB();
$a_set = $ilDB->query('SELECT * FROM ' . \LiveVoting\Conf\xlvoConfOld::TABLE_NAME);
while ($data = $ilDB->fetchObject($a_set)) {
    \LiveVoting\Conf\xlvoConf::set($data->config_key, $data->config_value);
}
?>
<#17>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

xlvoVotingConfig::updateDB();
?>
<#18>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Voter\xlvoVoter::updateDB();
?>
<#19>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Player\xlvoPlayer::updateDB();
\LiveVoting\Vote\xlvoVote::updateDB();
\LiveVoting\Option\xlvoOption::updateDB();
?>
<#20>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

xlvoVotingConfig::updateDB();
$xlvo_conf_table_name = xlvoVotingConfig::TABLE_NAME;
$frozen_behaviour = xlvoVotingConfig::B_FROZEN_ALWAY_OFF;
$results_behaviour = xlvoVotingConfig::B_RESULTS_ALWAY_OFF;
$q = "UPDATE {$xlvo_conf_table_name} SET frozen_behaviour={$frozen_behaviour}, results_behaviour={$results_behaviour}";
$ilDB->manipulate($q);
?>
<#21>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Player\xlvoPlayer::updateDB();
?>
<#22>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Voting\xlvoVoting::updateDB();
$xlvo_voting_table_name = \LiveVoting\Voting\xlvoVoting::TABLE_NAME;
$default = \LiveVoting\Voting\xlvoVoting::ROWS_DEFAULT;
$q = "UPDATE {$xlvo_voting_table_name} SET columns = {$default}";
$ilDB->manipulate($q);
/**
 * @var $xlvoVoting \LiveVoting\Voting\xlvoVoting
 */
foreach (\LiveVoting\Voting\xlvoVoting::get() as $xlvoVoting) {
	$xlvoVoting->renegerateOptionSorting();
}

?>
<#23>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
\LiveVoting\Player\xlvoPlayer::updateDB();
?>
<#24>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
$ilDB->manipulate("UPDATE " . xlvoVotingConfig::TABLE_NAME . " SET frozen_behaviour = 0, results_behaviour = 0");
?>
<#25>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Player\xlvoPlayer::updateDB();
\LiveVoting\Vote\xlvoVote::updateDB();
?>
<#26>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\Round\xlvoRound::updateDB();
?>
<#27>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

\LiveVoting\User\xlvoVoteHistoryObject::updateDB();
?>
<#28>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

xlvoVotingConfig::updateDB();
?>
<#29>
<?php

/**
 * @var $xlvoVoting xlvoVoting
 * @var $xlvoVote   xlvoVote
 */
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

foreach (\LiveVoting\Voting\xlvoVoting::where(array( 'obj_id' => 0 ), '>')->get() as $xlvoVoting) {
	$list = \LiveVoting\Vote\xlvoVote::where(array(
		"round_id"  => null,
		"voting_id" => $xlvoVoting->getId(),
	));
	if ($list->hasSets()) {
		$latestRound = \LiveVoting\Round\xlvoRound::getLatestRound($xlvoVoting->getObjId());
		foreach ($list->get() as $xlvoVote) {
			$xlvoVote->setRoundId($latestRound->getId());
			$xlvoVote->update();
		}
	}
}
?>
<#30>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

xlvoVotingConfig::updateDB();
$configs = xlvoVotingConfig::get();

/**
 * @var $config xlvoVotingConfig
 */
foreach($configs as $config)
{
    $config->setShowAttendees(false);
    $config->update();
}
?>
<#31>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';


$ilDB->addIndex(\LiveVoting\Voter\xlvoVoter::TABLE_NAME, array('player_id', 'user_identifier'), 'in1');
$ilDB->addIndex(\LiveVoting\Round\xlvoRound::TABLE_NAME, array('obj_id'), 'in1');
$ilDB->addIndex(\LiveVoting\Option\xlvoOption::TABLE_NAME, array('voting_id'), 'in1');
?>
<#32>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

use \LiveVoting\Conf\xlvoConf;

xlvoConf::set(xlvoConf::F_USE_GLOBAL_CACHE, 1);
?>
<#33>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
\LiveVoting\Voting\xlvoVoting::updateDB();
?>
<#34>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
\LiveVoting\Voting\xlvoVoting::updateDB();
?>
<#35>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';
\LiveVoting\Voting\xlvoVoting::updateDB();
?>
<#36>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

use LiveVoting\Pin\xlvoPuk;

xlvoVotingConfig::updateDB();

foreach (xlvoVotingConfig::get() as $xlvoVotingConfig) {
	/**
	 * @var xlvoVotingConfig $xlvoVotingConfig
	 */

	if (empty($xlvoVotingConfig->getPuk())) {
		$xlvoPuk = new xlvoPuk();

		$xlvoVotingConfig->setPuk($xlvoPuk->getPin());

		$xlvoVotingConfig->update();
	}
}
?>
<#37>
<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/vendor/autoload.php';

use LiveVoting\Pin\xlvoPuk;

xlvoVotingConfig::updateDB();

foreach (xlvoVotingConfig::get() as $xlvoVotingConfig) {
	/**
	 * @var xlvoVotingConfig $xlvoVotingConfig
	 */

	$xlvoPuk = new xlvoPuk();

	if (empty($xlvoVotingConfig->getPuk()) || strlen($xlvoVotingConfig->getPuk()) < $xlvoPuk->getPinLength()) {
		$xlvoVotingConfig->setPuk($xlvoPuk->getPin());

		$xlvoVotingConfig->update();
	}
}
?>
