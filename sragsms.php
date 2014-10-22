<?php

require_once('include.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingSragSMS.php');

$sms = new ilLiveVotingSragSMS(file_get_contents('php://input'));

?>