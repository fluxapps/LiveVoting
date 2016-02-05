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
	 * @var bool
	 */
	protected $show_option = false;
	/**
	 * @var string
	 */
	protected $base_url = '';


	/**
	 * xlvoInputMobileGUI constructor.
	 * @param xlvoVoting $voting
	 */
	public function __construct(xlvoVoting $voting, $base_url = '') {
		$this->voting = $voting;
		$this->base_url = $base_url;
	}


	/**
	 * @param xlvoVoting $voting
	 * @param string $base_url
	 * @return xlvoFreeInputMobileGUI
	 */
	public static function getInstance(xlvoVoting $voting, $base_url = '') {
		$class = xlvoQuestionTypes::getClassName($voting->getVotingType());
		/**
		 * @var $class_name xlvoFreeInputMobileGUI
		 * @var $mobile_view xlvoFreeInputMobileGUI
		 */
		$class_name = 'xlvo' . $class . 'MobileGUI';
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/' . $class . '/class.'
			. $class_name . '.php');

		$mobile_view = new $class_name($voting);
		return $mobile_view;
	}


	/**
	 * @return boolean
	 */
	public function isShowOption() {
		return $this->show_option;
	}


	/**
	 * @param boolean $show_option
	 */
	public function setShowOption($show_option) {
		$this->show_option = $show_option;
	}


	abstract public function getHTML();


//	abstract public function initJS();
}

