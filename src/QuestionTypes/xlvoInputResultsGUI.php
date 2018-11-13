<?php

namespace LiveVoting\QuestionTypes;

use ilException;
use ilLiveVotingPlugin;
use LiveVoting\QuestionTypes\CorrectOrder\xlvoCorrectOrderResultsGUI;
use LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputResultsGUI;
use LiveVoting\QuestionTypes\FreeOrder\xlvoFreeOrderResultsGUI;
use LiveVoting\QuestionTypes\NumberRange\xlvoNumberRangeResultsGUI;
use LiveVoting\QuestionTypes\SingleVote\xlvoSingleVoteResultsGUI;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Voting\xlvoVotingManager2;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoInputResultsGUI
 *
 * @package LiveVoting\QuestionTypes
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class xlvoInputResultsGUI {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
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
	 * @param xlvoVoting         $voting
	 */
	public function __construct(xlvoVotingManager2 $manager, xlvoVoting $voting) {
		$this->manager = $manager;
		$this->voting = $voting;
	}


	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected function txt($key) {
		return self::plugin()->translate($this->manager->getVoting()->getVotingType() . '_' . $key, 'qtype');
	}


	/**
	 * @param xlvoVotingManager2 $manager
	 *
	 * @return xlvoInputResultsGUI
	 * @throws ilException         Throws an ilException if no results gui class was found.
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
				throw new ilException('Could not find the results gui for the given voting.');
		}
	}


	/**
	 * @return string
	 */
	public abstract function getHTML();


	/**
	 * @param xlvoVote[] $votes
	 *
	 * @return string
	 *
	 * TODO: Usage?
	 */
	public abstract function getTextRepresentationForVotes(array $votes);
}
