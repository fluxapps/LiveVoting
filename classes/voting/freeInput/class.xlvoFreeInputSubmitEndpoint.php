<?php

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoInitialisation.php');
xlvoInitialisation::init();

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');

/**
 * @var $voter_gui xlvoVoterGUI
 */
$voter_gui = new xlvoVoterGUI();
/**
 * @var $posted_vote xlvoVote
 */
$posted_vote = new xlvoVote();
/**
 * @var $voting_manager xlvoVotingManager
 */
$voting_manager = new xlvoVotingManager();
/**
 * @var $vote xlvoVote
 */
$vote = new xlvoVote();

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
	/**
	 * @var xlvoOption $option
	 */
	$option = xlvoOption::find($posted_vote->getOptionId());
	/**
	 * @var xlvoVote[] $votes
	 */
	$votes = $voting_manager->getVotesOfUserOfOption($option->getVotingId(), $option->getId())->get();
	foreach ($votes as $vote) {
		$vote->delete();
	}
}

if (! $vote instanceof xlvoVote) {
	$vote = $posted_vote;
}

$votes = $voting_manager->getVotesOfUserOfOption($vote->getVotingId(), $vote->getOptionId())->getArray();

header('Content-type: application/json');
echo json_encode($votes);