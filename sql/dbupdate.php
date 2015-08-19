<#1>
<?php
$fields = array(
'id' => array(
'type' => 'integer',
'length' => 4,
'notnull' => true
)
);

$ilDB->createTable("rep_robj_xlvo_data", $fields);
$ilDB->addPrimaryKey("rep_robj_xlvo_data", array( "id" ));
$ilDB->createSequence("rep_robj_xlvo_data");
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

$ilDB->createTable("rep_robj_xlvo_vote", $fields);
$ilDB->addPrimaryKey("rep_robj_xlvo_vote", array( "id" ));
$ilDB->createSequence("rep_robj_xlvo_vote");
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
$ilDB->createTable("rep_robj_xlvo_option", $fields);
$ilDB->addPrimaryKey("rep_robj_xlvo_option", array( "id" ));
$ilDB->createSequence("rep_robj_xlvo_option");
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

$ilDB->createTable("rep_robj_xlvo_conf", $fields);
$ilDB->addPrimaryKey("rep_robj_xlvo_conf", array( "lvo_key" ));
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
$ilDB->renameTableColumn($pl->getConfigTableName(), 'lvo_key', 'config_key');
$ilDB->renameTableColumn($pl->getConfigTableName(), 'lvo_value', 'config_value');
$ilDB->modifyTableColumn($pl->getConfigTableName(), 'config_value', array(
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
