<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputResultsGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarFreeInputsGUI.php');

/**
 * Class xlvoFreeInputResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputResultsGUI extends xlvoInputResultsGUI {

	/**
	 * @return string
	 */
	public function getHTML() {

		$bars = new xlvoBarCollectionGUI();
		/**
		 * @var xlvoOption $option
		 */
		$option = $this->voting->getFirstVotingOption();

		/**
		 * @var xlvoVote[] $votes
		 */
		$votes = $this->voting_manager->getVotesOfOption($option->getId())->get();
		foreach ($votes as $vote) {
			$bars->addBar(new xlvoBarFreeInputsGUI($this->voting, $vote));
		}

		return $bars->getHTML();
	}
}
