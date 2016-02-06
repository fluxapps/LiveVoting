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
	 * @var xlvoVotingManager2
	 */
	protected $manager;


	public function __construct() {
		parent::__construct();
		$this->voting_manager = new xlvoVotingManager();
	}


	/**
	 * @param xlvoVotingManager2 $manager
	 * @param null $override_type
	 * @return xlvoQuestionTypesGUI
	 */
	public static function getInstance(xlvoVotingManager2 $manager, $override_type = null) {
		$class_type = xlvoQuestionTypes::getClassName($override_type ? $override_type : $manager->getVoting()->getVotingType());
		/**
		 * @var $class_name xlvoQuestionTypesGUI
		 * @var $gui xlvoQuestionTypesGUI
		 */
		$class_name = 'xlvo' . $class_type . 'GUI';
		$base = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/';
		require_once($base . $class_type . '/class.' . $class_name . '.php');

		$gui = new $class_name();
		$gui->setManager($manager);

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
	 * @return xlvoVotingManager2
	 */
	public function getManager() {
		return $this->manager;
	}


	/**
	 * @param xlvoVotingManager2 $manager
	 */
	public function setManager($manager) {
		$this->manager = $manager;
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
