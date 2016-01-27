<?php

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.xlvoInitialisation.php');
xlvoInitialisation::init();

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voter/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Option/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingManager.php');

/**
 * @var $tpl ilTemplate
 */
global $tpl;
/**
 * @var ilLiveVotingPlugin
 */
$pl = ilLiveVotingPlugin::getInstance();
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

$posted_vote->setId((int)$_REQUEST['vote_id']);
$posted_vote->setOptionId((int)$_REQUEST['option_id']);
$posted_vote->setStatus(xlvoVote::STAT_ACTIVE);
$obj_id = $_REQUEST['object_id'];

try {
	/**
	 * @var xlvoOption $option
	 */
	$option = $voting_manager->getOption($_REQUEST['option_id']);
} catch (xlvoVotingManagerException $e) {
	header('Content-type: text/html');
	echo $tpl->getMessageHTML($pl->txt('error_load_voting_failed'), 'failure');

	return false;
}

$success = $voter_gui->vote($posted_vote);

if ($success) {
	/**
	 * @var xlvoVote $votes
	 */
	$votes = $voting_manager->getVotesOfUserofVoting($option->getVotingId())->getArray();

	header('Content-type: application/json');
	echo json_encode($votes);
} else {
	header('Content-type: text/html');
	// votingId is NULL to reload Voting page
	echo $voter_gui->showVoting($obj_id, NULL, 'error_vote_failed');
}
