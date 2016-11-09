<?php

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
	 * @var xlvoVotingManager
	 */
	protected $voting_manager;
	/**
	 * @var bool
	 */
	protected $shuffle_results = false;
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
	 * @return xlvoFreeInputResultsGUI
	 */
	public static function getInstance(xlvoVotingManager2 $manager) {
		$class = xlvoQuestionTypes::getClassName($manager->getVoting()->getVotingType());
		/**
		 * @var $class_name xlvoFreeInputResultsGUI
		 * @var $subform    xlvoFreeInputResultsGUI
		 */
		$class_name = 'xlvo' . $class . 'ResultsGUI';
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/' . $class . '/class.'
		             . $class_name . '.php');

		$subform = new $class_name($manager, $manager->getVoting());

		return $subform;
	}


	/**
	 * @return boolean
	 */
	public function isShuffleResults() {
		return $this->shuffle_results;
	}

	/**
	 * @param boolean $shuffle_results
	 */
	public function setShuffleResults($shuffle_results) {
		$this->shuffle_results = $shuffle_results;
	}


	abstract public function getHTML();


	/**
	 * @param $votes xlvoVote[]
	 * @return string
	 */
	abstract public function getTextRepresentationForVotes($votes);
}