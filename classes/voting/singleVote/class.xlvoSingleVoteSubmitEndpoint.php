<?php

chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoInitialisation.php');
xlvoInitialisation::init();

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');

$voter_gui = new xlvoVoterGUI();
$posted_vote = new xlvoVote();
$voting_manager = new xlvoVotingManager();

$posted_vote->setId((int)$_REQUEST['vote_id']);
$posted_vote->setOptionId((int)$_REQUEST['option_id']);
$posted_vote->setStatus(xlvoVote::STAT_ACTIVE);

//$option = $voting_manager->getOption($posted_vote->getOptionId());

/**
 * @var xlvoVote $vote
 */
$vote = $voter_gui->vote($posted_vote);
/**
 * @var xlvoVote $votes
 */
$votes = $voting_manager->getVotesOfUserofVoting($vote->getVotingId())->getArray();

header('Content-type: application/json');
echo json_encode($votes);