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
	/**
	 * @var bool
	 */
	protected $show_question = true;
	/**
	 * @var bool
	 */
	protected $has_solution = false;


	/**
	 * @param $key
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('qtype_' . $this->manager->getVoting()->getVotingType() . '_' . $key);
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
		 * @var $gui        xlvoQuestionTypesGUI
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
	 * @return boolean
	 */
	public function isShowQuestion() {
		return $this->show_question;
	}


	/**
	 * @param boolean $show_question
	 */
	public function setShowQuestion($show_question) {
		$this->show_question = $show_question;
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


	//
	// Custom Buttons
	//

	/**
	 * @param $button_id
	 * @param $data
	 */
	public function handleButtonCall($button_id, $data) {
		$this->saveButtonState($button_id, $data);
	}


	/**
	 * @return array
	 */
	protected function getButtonsStates() {
		$xlvoPlayer = $this->getManager()->getPlayer();

		return $xlvoPlayer->getButtonStates();
	}


	/**
	 * @return ilButtonBase[]
	 */
	public function getButtonInstances() {
		return array();
	}


	/**
	 * @return bool
	 */
	public function hasButtons() {
		return (count($this->getButtonInstances()) > 0);
	}


	/**
	 * @param $button_id
	 * @param $state
	 */
	protected function saveButtonState($button_id, $state) {
		$xlvoPlayer = $this->getManager()->getPlayer();
		$states = $xlvoPlayer->getButtonStates();
		$states[$button_id] = $state;
		$xlvoPlayer->setButtonStates($states);
		$xlvoPlayer->update();
	}
}
