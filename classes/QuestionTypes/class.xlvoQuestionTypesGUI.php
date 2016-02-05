<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoGUI.php');

/**
 * Class xlvoQuestionTypesGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 */
abstract class xlvoQuestionTypesGUI extends xlvoGUI {

	const CMD_SUBMIT = 'submit';
	/**
	 * @var xlvoVoting
	 */
	protected $voting;
	/**
	 * @var xlvoVotingManager
	 */
	protected $voting_manager;
	/**
	 * @var xlvoVotingManager2
	 */
	protected $manager;


	public function __construct() {
		parent::__construct();
		$this->voting_manager = new xlvoVotingManager();
	}


	/**
	 * @param $voting_type
	 * @param xlvoVoting|null $xlvoVoting
	 * @return xlvoQuestionTypesGUI
	 */
	public static function getInstance($voting_type, xlvoVoting $xlvoVoting = null) {
		$class_type = xlvoQuestionTypes::getClassName($voting_type);
		/**
		 * @var $class_name xlvoQuestionTypesGUI
		 * @var $gui xlvoQuestionTypesGUI
		 */
		$class_name = 'xlvo' . $class_type . 'GUI';
		$base = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/';
		require_once($base . $class_type . '/class.' . $class_name . '.php');

		$gui = new $class_name();
		$gui->setVoting($xlvoVoting);

		return $gui;
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();

		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);

				$this->{$cmd}();
				if ($cmd == self::CMD_SUBMIT) {
					$this->afterSubmit();
				}
				break;
		}
		if ($this->is_api_call) {
			$this->tpl->show();
		}
	}


	/**
	 * @return xlvoVoting
	 */
	public function getVoting() {
		return $this->voting;
	}


	/**
	 * @param xlvoVoting $voting
	 */
	public function setVoting($voting) {
		$this->voting = $voting;
	}


	/**
	 * @description add JS to the HEAD
	 */
	abstract public function initJS();


	/**
	 * @description Vote
	 */
	abstract protected function submit();


	protected function afterSubmit() {
		$this->ctrl->redirect(new xlvoVoter2GUI(), xlvoVoter2GUI::CMD_START_VOTER_PLAYER);
	}


	/**
	 * @return string
	 */
	abstract public function getMobileHTML();
}
