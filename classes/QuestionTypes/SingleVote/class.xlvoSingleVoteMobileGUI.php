<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputMobileGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarCollectionGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarOptionGUI.php');

/**
 * Class xlvoSingleVoteMobileGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoSingleVoteMobileGUI extends xlvoInputMobileGUI {

	/**
	 * @var int
	 */
	protected $answer_count = 64;


	/**
	 * @return string
	 */
	public function getHTML() {
		$bars = new xlvoBarCollectionGUI();
		foreach ($this->voting->getVotingOptions() as $option) {
			$this->answer_count ++;
			$bars->addBar(new xlvoBarOptionGUI($this->voting, $option, (chr($this->answer_count))));
		}

		return $bars->getHTML();
	}
}
