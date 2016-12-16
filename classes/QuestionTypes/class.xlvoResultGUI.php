<?php

abstract class xlvoResultGUI {

	/**
	 * @var xlvoVoting
	 */
	protected $voting;

	/**
	 * @var xlvoOption[]
	 */
	protected $options;

	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;

	/**
	 * xlvoResultGUI constructor.
	 * @param $voting xlvoVoting
	 */
	public function __construct($voting) {
		$this->voting = $voting;
		$this->options = $voting->getVotingOptions();
		$this->pl = ilLiveVotingPlugin::getInstance();
	}

	/**
	 * @param $votes xlvoVote[]
	 * @return string
	 */
	public abstract function getTextRepresentation($votes);

	/**
	 * @param $voting xlvoVoting
	 * @return xlvoResultGUI
	 */
	public static function getInstance($voting) {
		$class = xlvoQuestionTypes::getClassName($voting->getVotingType());
		/**
		 * @var $class_name xlvoFreeInputResultsGUI
		 * @var $subform    xlvoFreeInputResultsGUI
		 */
		$class_name = 'xlvo' . $class . 'ResultGUI';
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/' . $class . '/class.'
			. $class_name . '.php');

		$subform = new $class_name($voting);

		return $subform;
	}
}