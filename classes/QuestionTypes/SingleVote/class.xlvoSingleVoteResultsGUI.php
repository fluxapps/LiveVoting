<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputResultsGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/SingleVote/class.xlvoSingleVoteGUI.php');

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
		$max = $this->manager->getMaxCountOfVotes();
		foreach ($this->voting->getVotingOptions() as $xlvoOption) {
			$answer_count ++;
			/**
			 * @var xlvoVote $votes
			 */
			$votes = $this->manager->countVotesOfOption($xlvoOption->getId());

			$xlvoBarPercentageGUI = new xlvoBarPercentageGUI();
			$xlvoBarPercentageGUI->setShowAbsolute($this->isShowAbsolute());
			$xlvoBarPercentageGUI->setTitle($xlvoOption->getText());
			$xlvoBarPercentageGUI->setId($xlvoOption->getId());
			$xlvoBarPercentageGUI->setVotes($votes);
			$xlvoBarPercentageGUI->setTotal($total);
			$xlvoBarPercentageGUI->setMax($max);

			$bars->addBar($xlvoBarPercentageGUI);
		}

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($this->manager->countVotes());

		return $bars->getHTML();
	}


	/**
	 * @return array
	 */
	protected function getButtonsStates() {
		return $this->manager->getPlayer()->getButtonStates();
	}


	/**
	 * @return bool
	 */
	protected function isShowAbsolute() {
		$states = $this->getButtonsStates();

		return ($this->manager->getPlayer()->isShowResults() && (bool)$states[xlvoSingleVoteGUI::BUTTON_TOGGLE_PERCENTAGE]);
	}
}
