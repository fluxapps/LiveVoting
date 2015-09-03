<?php

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoInitialisation.php');
xlvoInitialisation::initILIAS();

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');

$voter_gui = new xlvoVoterGUI();
$posted_vote = new xlvoVote();
$voting_manager = new xlvoVotingManager();

$request_type = $_REQUEST['type'];
$posted_vote->setFreeInput($_REQUEST['free_input']);
$posted_vote->setId((int)$_REQUEST['vote_id']);
$posted_vote->setOptionId((int)$_REQUEST['option_id']);

if ($request_type == 'unvote') {
	$posted_vote->setStatus(xlvoVote::STAT_INACTIVE);
	$vote = $voter_gui->vote($posted_vote);
}
if ($request_type == 'vote') {
	$posted_vote->setStatus(xlvoVote::STAT_ACTIVE);
	$vote = $voter_gui->vote($posted_vote);
}
if ($request_type == 'delete_all') {
	$option = xlvoOption::find($posted_vote->getOptionId());
	$votes = $voting_manager->getVotes($option->getVotingId(), $option->getId(), true)->get();
	foreach ($votes as $vote) {
		$vote->delete();
	}
}

$votes = $voting_manager->getVotes($vote->getVotingId(), $vote->getOptionId(), true)->getArray();

header('Content-type: application/json');
echo json_encode($votes);