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

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
$pl = new ilLiveVotingPlugin();
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
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
xlvoOption::installDB();
?>
<#10>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVote.php');
xlvoVote::installDB();
?>
<#11>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoting.php');
xlvoVoting::installDB();
?>
<#12>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingConfig.php');
xlvoVotingConfig::installDB();
?>
<#13>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoPlayer.php');
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
//require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVoting.php');
//ilObjLiveVoting::dataTransferRefactoring();
?>
