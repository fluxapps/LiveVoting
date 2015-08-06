<?php

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./include/inc.header.php');

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');

$voter_gui = new xlvoVoterGUI();
$posted_vote = new xlvoVote();
$posted_vote->setId((int)$_POST['vote_id']);
$posted_vote->setOptionId((int)$_POST['option_id']);
$vote = $voter_gui->vote($posted_vote);
$voting_manager = new xlvoVotingManager();
$votes = $voting_manager->getVotes($vote->getVotingId(), NULL, true)->getArray();

header('Content-type: application/json');
echo json_encode($votes);