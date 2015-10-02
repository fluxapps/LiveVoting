<?php

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoInitialisation.php');
xlvoInitialisation::init();

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoPlayerGUI.php');

$voter_gui = new xlvoVoterGUI();
$player_gui = new xlvoPlayerGUI();
$voting_manager = new xlvoVotingManager();
$posted_type = $_REQUEST['type_player'];
$posted_voting_id = $_REQUEST['voting_id_current'];
$posted_object_id = $_REQUEST['object_id'];
$posted_pin = $_REQUEST['pin_input'];

/**
 * Voter
 */

if ($posted_type == 'get_voting_data') {
	$data = $voter_gui->getVotingData($posted_object_id);
	header('Content-type: application/json');
	echo json_encode($data);
}

if ($posted_type == 'access_screen') {
	header('Content-type: text/html');
	if (xlvoInitialisation::getCookiePIN()) {
		echo $voter_gui->accessVoting($_COOKIE['xlvo_pin']);
	} else {
		echo $voter_gui->showAccessScreen(false);
	}
}

if ($posted_type == 'access_voting') {
	header('Content-type: text/html');
	xlvoInitialisation::setCookiePIN($posted_pin);
	echo $voter_gui->accessVoting($posted_pin);
}

if ($posted_type == 'voting_screen') {
	header('Content-type: text/html');

	try {
		$player = $voting_manager->getPlayer($posted_object_id);

		if ($player->isFrozenOrUnattended()) {
			echo $voter_gui->showWaitForQuestionScreen($posted_object_id);
		} else {
			echo $voter_gui->showVoting($posted_object_id, $posted_voting_id);
		}
	} catch (xlvoVotingManagerException $e) {
		echo $voter_gui->showInfoScreen($posted_object_id, 'not_available_screen');
	}
}

if ($posted_type == 'waiting_screen') {
	header('Content-type: text/html');
	echo $voter_gui->showInfoScreen($posted_object_id, $posted_type);
}

if ($posted_type == 'not_running_screen') {
	header('Content-type: text/html');
	echo $voter_gui->showInfoScreen($posted_object_id, $posted_type);
}

if ($posted_type == 'not_available_screen') {
	header('Content-type: text/html');
	echo $voter_gui->showInfoScreen($posted_object_id, $posted_type);
}

if ($posted_type == 'start_of_voting_screen') {
	header('Content-type: text/html');
	echo $voter_gui->showInfoScreen($posted_object_id, $posted_type);
}

if ($posted_type == 'end_of_voting_screen') {
	header('Content-type: text/html');
	echo $voter_gui->showInfoScreen($posted_object_id, $posted_type);
}

/**
 * Player
 */

if ($posted_type == 'load_results') {
	header('Content-type: text/html');
	echo $player_gui->showVoting($posted_voting_id);
}

if ($posted_type == 'load_player_info') {
	header('Content-type: text/html');
	echo $voting_manager->isVotingAvailable($posted_object_id);
}

if ($posted_type == 'freeze_voting') {
	$success = $player_gui->freeze($posted_object_id);
	header('Content-type: text/html');
	echo $success;
}

if ($posted_type == 'unfreeze_voting') {
	$success = $player_gui->unfreeze($posted_object_id);
	header('Content-type: text/html');
	echo $success;
}

if ($posted_type == 'reset_voting') {
	$success = $player_gui->resetVotes($posted_object_id, $posted_voting_id);
	header('Content-type: text/html');
	echo $success;
}