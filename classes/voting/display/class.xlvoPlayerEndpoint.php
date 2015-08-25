<?php

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./include/inc.header.php');

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoPlayerGUI.php');

$voter_gui = new xlvoVoterGUI();
$posted_vote = new xlvoVote();
$voting_manager = new xlvoVotingManager();
$player_gui = new xlvoPlayerGUI();

$posted_type = $_POST['type_player'];
$posted_voting_id = $_POST['voting_id_current'];
$posted_object_id = $_POST['object_id'];

/**
 * Voter
 */

if ($posted_type == 'load_voting_screen') {
	$voting_id_player = $player_gui->getActiveVoting($posted_object_id);
	if ($posted_voting_id == $voting_id_player) {
		header('Content-type: text/html');
		echo '';
	} else {
		header('Content-type: text/html');
		echo $voter_gui->showVoting($voting_id_player);
	}
}

if ($posted_type == 'load_waiting_screen') {
	header('Content-type: text/html');
	echo $voter_gui->waitingScreen($posted_object_id);
}

if ($posted_type == 'get_voting_data') {
	$config = $voting_manager->getVotingConfig($posted_object_id);
	$player = xlvoPlayer::where(array( 'obj_id' => $posted_object_id ))->first();
	$data = array(
		'voIsFrozen' => $config->isFrozen(),
		'voIsReset' => $player->isReset(),
		'voStatus' => $player->getStatus(),
		'voHasAccess' => 1,
		'voIsAvailable' => 1
	);
	header('Content-type: application/json');
	echo json_encode($data);
}

/**
 * Player
 */

if ($posted_type == 'load_results') {
	header('Content-type: text/html');
	echo $player_gui->showVoting($posted_voting_id);
}

if ($posted_type == 'freeze_voting') {
	header('Content-type: text/html');
	$player_gui->freeze($posted_object_id);
}

if ($posted_type == 'unfreeze_voting') {
	header('Content-type: text/html');
	$player_gui->unfreeze($posted_object_id);
}

if ($posted_type == 'reset_voting') {
	$player_gui->resetVotes($posted_voting_id);
	header('Content-type: text/html');
	echo '';
}