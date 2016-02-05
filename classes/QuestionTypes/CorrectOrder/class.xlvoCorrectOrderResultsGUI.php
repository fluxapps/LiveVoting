<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/SingleVote/class.xlvoSingleVoteResultsGUI.php');

/**
 * Class xlvoCorrectOrderResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoCorrectOrderResultsGUI extends xlvoSingleVoteResultsGUI {


	/**
	 * @return string
	 */
	public function getHTML() {
		return parent::getHTML();
	}
}
