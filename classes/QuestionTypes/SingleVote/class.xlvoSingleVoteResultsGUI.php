<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputResultsGUI.php');

/**
 * Class xlvoSingleVoteResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoSingleVoteResultsGUI extends xlvoInputResultsGUI {

	/**
	 * @return string
	 */
	public function getHTML() {
		/**
		 * @var xlvoBarCollectionGUI
		 */
		$answer_count = 64;
		$bars = new xlvoBarCollectionGUI();
		$total = $this->manager->countVotes();
		foreach ($this->voting->getVotingOptions() as $option) {
			$answer_count ++;
			/**
			 * @var xlvoVote $votes
			 */
			$votes = $this->manager->getVotesOfOption($option->getId());
			$bars->addBar(xlvoBarPercentageGUI::getInstanceFromOption($option, count($votes), $total, chr($answer_count)));
		}

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($this->manager->countVotes());

		return $bars->getHTML();
	}
}
