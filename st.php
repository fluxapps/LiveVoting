<?php
require_once('include.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingStressTestSMS.php');
$sms = new ilLiveVotingStressTestSMS($_GET);

?>