<?php

/**
 * Class xlvoInputMobileGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class xlvoInputMobileGUI {

	/**
	 * @var xlvoVoting
	 */
	protected $voting;


	/**
	 * xlvoInputMobileGUI constructor.
	 * @param xlvoVoting $voting
	 */
	public function __construct(xlvoVoting $voting) {
		$this->voting = $voting;
	}


	/**
	 * @param xlvoVoting $voting
	 * @return xlvoFreeInputSubFormGUI
	 */
	public static function getInstance(xlvoVoting $voting) {
		$class = xlvoVotingType::getClassName($voting->getVotingType());
		/**
		 * @var $class_name xlvoFreeInputSubFormGUI
		 * @var $subform xlvoFreeInputSubFormGUI
		 */
		$class_name = 'xlvo' . $class . 'MobileGUI';
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/' . $class . '/class.'
			. $class_name . '.php');

		$subform = new $class_name($voting);
		return $subform;
	}


	abstract public function getHTML();
}

