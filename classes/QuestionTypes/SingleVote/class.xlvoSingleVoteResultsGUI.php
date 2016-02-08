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
		foreach ($this->voting->getVotingOptions() as $option) {
			$answer_count ++;
			/**
			 * @var xlvoVote $votes
			 */
			$votes = $this->manager->getVotesOfVoting();

			$bars->addBar(new xlvoBarPercentageGUI($this->manager->getVoting(), $option, $votes, chr($answer_count)));
		}

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes(10);

		return $bars->getHTML();
	}
}
