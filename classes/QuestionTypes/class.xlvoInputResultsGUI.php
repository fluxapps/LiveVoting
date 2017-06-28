<?php

use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Voting\xlvoVotingManager2;

/**
 * Class xlvoInputResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class xlvoInputResultsGUI {

	/**
	 * @var xlvoVoting
	 */
	protected $voting;
	/**
	 * @var xlvoVotingManager2
	 */
	protected $manager;


	/**
	 * xlvoInputResultsGUI constructor.
	 *
	 * @param xlvoVotingManager2 $manager
	 * @param xlvoVoting $voting
	 */
	public function __construct(xlvoVotingManager2 $manager, xlvoVoting $voting) {
		$this->manager = $manager;
		$this->voting = $voting;
		$this->pl = ilLiveVotingPlugin::getInstance();
	}


	/**
	 * @param $key
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('qtype_' . $this->manager->getVoting()->getVotingType() . '_' . $key);
	}


	/**
	 * @param xlvoVotingManager2 $manager
	 * @return xlvoInputResultsGUI
     * @throws \ilException         Throws an \ilException if no results gui class was found.
     */
	public static function getInstance(xlvoVotingManager2 $manager) {
		$class = xlvoQuestionTypes::getClassName($manager->getVoting()->getVotingType());
        switch ($class) {
	        case xlvoQuestionTypes::CORRECT_ORDER:
                return new xlvoCorrectOrderResultsGUI($manager, $manager->getVoting());
	        case xlvoQuestionTypes::FREE_INPUT:
                return new xlvoFreeInputResultsGUI($manager, $manager->getVoting());
	        case xlvoQuestionTypes::FREE_ORDER:
                return new xlvoFreeOrderResultsGUI($manager, $manager->getVoting());
	        case xlvoQuestionTypes::SINGLE_VOTE:
                return new xlvoSingleVoteResultsGUI($manager, $manager->getVoting());
	        case xlvoQuestionTypes::NUMBER_RANGE:
	        	return new xlvoNumberRangeResultsGUI($manager, $manager->getVoting());
            default:
                throw new \ilException('Could not find the results gui for the given voting.');
        }
	}

	abstract public function getHTML();


	/**
	 * @param $votes xlvoVote[]
	 * @return string
	 */
	abstract public function getTextRepresentationForVotes($votes);
}