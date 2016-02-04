<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputResultsGUI.php');

/**
 * Class xlvoSingleVoteResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoSingleVoteResultsGUI extends xlvoInputResultsGUI {

	protected $answer_count = 64;


	/**
	 * @return string
	 */
	public function getHTML() {
		/**
		 * @var xlvoBarCollectionGUI
		 */
		$bars = new xlvoBarCollectionGUI();

		/**
		 * @var xlvoOption $options
		 */
		$options = $this->voting->getVotingOptions()->get();
		foreach ($options as $option) {
			$this->answer_count ++;
			/**
			 * @var xlvoVote $votes
			 */
			$votes = $this->voting_manager->getVotesOfVoting($this->voting->getId());


			$bars->addBar(new xlvoBarPercentageGUI($this->voting, $option, $votes, chr($this->answer_count)));
		}

		return $bars->getHTML();
	}
}
