

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
	 * xlvoFreeInputDisplayGUI constructor.
	 * @param xlvoVoting $voting
	 * @param xlvoVotingManager $voting_manager
	 */
	public function __construct(xlvoVoting $voting, xlvoVotingManager $voting_manager) {
		$this->voting = $voting;
		$this->voting_manager = $voting_manager;
	}


	/**
	 * @param xlvoVoting $voting
	 * @param xlvoVotingManager $voting_manager
	 * @return xlvoFreeInputSubFormGUI
	 */
	public static function getInstance(xlvoVoting $voting, xlvoVotingManager $voting_manager) {
		$class = xlvoVotingType::getClassName($voting->getVotingType());
		/**
		 * @var $class_name xlvoFreeInputSubFormGUI
		 * @var $subform xlvoFreeInputSubFormGUI
		 */
		$class_name = 'xlvo' . $class . 'ResultsGUI';
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/' . $class . '/class.'
			. $class_name . '.php');

		$subform = new $class_name($voting, $voting_manager);
		return $subform;
	}


	abstract public function getHTML();
}