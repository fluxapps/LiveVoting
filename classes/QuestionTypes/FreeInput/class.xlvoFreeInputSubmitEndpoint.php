<?php

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.xlvoInitialisation.php');
xlvoInitialisation::init();

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVoterGUI.php');
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
/**
 * @var $vote xlvoVote
 */
$vote = new xlvoVote();

$request_type = $_REQUEST['type'];

$posted_vote->setFreeInput($_REQUEST['free_input']);
$posted_vote->setId((int)$_REQUEST['vote_id']);
$posted_vote->setOptionId((int)$_REQUEST['option_id']);
$obj_id = $_REQUEST['object_id'];

if ($request_type == 'unvote') {
	$posted_vote->setStatus(xlvoVote::STAT_INACTIVE);

	$success = $voter_gui->vote($posted_vote);

	if ($success) {
		header('Content-type: text/html');
		echo '';
	} else {
		header('Content-type: text/html');
		// votingId is NULL to reload Voting page
		echo $voter_gui->showVoting($obj_id, NULL, 'error_vote_reset_failed');
	}
}

if ($request_type == 'vote') {

	$posted_vote->setStatus(xlvoVote::STAT_ACTIVE);

	$success = $voter_gui->vote($posted_vote);

	if ($success) {

		try {
			$option = $voting_manager->getOption($_REQUEST['option_id']);
		} catch (xlvoVotingManagerException $e) {
			header('Content-type: text/html');
			echo $tpl->getMessageHTML($pl->txt('error_load_voting_failed'), 'failure');

			return false;
		}

		$votes = $voting_manager->getVotesOfUserOfOption($option->getVotingId(), $option->getId())->getArray();
		header('Content-type: application/json');
		echo json_encode($votes);
	} else {
		header('Content-type: text/html');
		// votingId is NULL to reload Voting page
		echo $voter_gui->showVoting($obj_id, NULL, 'error_vote_failed');
	}
}

if ($request_type == 'vote_multi') {

	try {
		$option = $voting_manager->getOption($_REQUEST['option_id']);
	} catch (xlvoVotingManagerException $e) {
		header('Content-type: text/html');
		echo $tpl->getMessageHTML($pl->txt('error_load_voting_failed'), 'failure');

		return false;
	}

	/**
	 * @var $existing_votes xlvoVote[]
	 */
	$existing_votes = $voting_manager->getVotesOfUserOfOption($option->getVotingId(), $option->getId())->getArray();
	$posted_votes = $_REQUEST['votes'];

	// delete votes
	foreach ($existing_votes as $vo) {
		$id_to_delete_found = array_search($vo['id'], array_column($posted_votes, 'vote_id'));

		$failure = false;

		if ($id_to_delete_found === false) {
			$vote_to_delete = new xlvoVote();
			$vote_to_delete->setOptionId($vo['option_id']);
			$vote_to_delete->setId($vo['id']);
			$vote_to_delete->setFreeInput($vo['free_input']);
			$vote_to_delete->setStatus(xlvoVote::STAT_INACTIVE);
			$success = $voter_gui->vote($vote_to_delete);
			if (! $success) {
				$failure = true;
			}
		}
	}

	// create and update votes
	foreach ($posted_votes as $p_vote) {
		$is_found = array_search($p_vote['vote_id'], array_column($existing_votes, 'id'));
		if ($is_found !== false) {
			// create vote
			$vote_to_save = new xlvoVote();
			$vote_to_save->setOptionId($_REQUEST['option_id']);
			$vote_to_save->setId($p_vote['vote_id']);
			$vote_to_save->setFreeInput($p_vote['free_input']);
			$vote_to_save->setStatus(xlvoVote::STAT_ACTIVE);
			$success = $voter_gui->vote($vote_to_save);
			if (! $success) {
				$failure = true;
			}
		} else {
			// update vote
			$vote_to_update = new xlvoVote();
			$vote_to_update->setOptionId($_REQUEST['option_id']);
			$vote_to_update->setId(0);
			$vote_to_update->setFreeInput($p_vote['free_input']);
			$vote_to_update->setStatus(xlvoVote::STAT_ACTIVE);
			$success = $voter_gui->vote($vote_to_update);
			if (! $success) {
				$failure = true;
			}
		}
	}

	if (! $failure) {
		header('Content-type: text/html');
		// votingId is NULL to reload Voting page
		echo $voter_gui->showVoting($obj_id, NULL);
	} else {
		header('Content-type: text/html');
		// votingId is NULL to reload Voting page
		echo $voter_gui->showVoting($obj_id, NULL, 'error_free_input_multi_vote_failed');
	}
}

if ($request_type == 'delete_all') {
	/**
	 * @var xlvoOption $option
	 */
	$option = xlvoOption::find($posted_vote->getOptionId());

	$failure = false;

	/**
	 * @var xlvoVote[] $votes
	 */
	$votes = $voting_manager->getVotesOfUserOfOption($option->getVotingId(), $option->getId())->get();
	foreach ($votes as $vote) {
		$vote->setStatus(xlvoVote::STAT_INACTIVE);
		$success = $voter_gui->vote($vote);
		if (! $success) {
			$failure = true;
		}
	}

	if (! $failure) {
		header('Content-type: text/html');
		// votingId is NULL to reload Voting page
		echo $voter_gui->showVoting($obj_id, NULL);
	} else {
		header('Content-type: text/html');
		// votingId is NULL to reload Voting page
		echo $voter_gui->showVoting($obj_id, NULL, 'error_vote_reset_failed');
	}
}
