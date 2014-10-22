<?php

require_once('include.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingAspSMS.php');

$sms = new ilLiveVotingAspSMS($_GET);

?>