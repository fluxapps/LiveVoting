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
		$bars = new xlvoBarCollectionGUI();

		$bars->setShowTotalVoters(true);
		$bars->setTotalVoters($this->manager->countVoters());

		$max = $this->manager->countVoters();

		foreach ($this->voting->getVotingOptions() as $xlvoOption) {
			$xlvoBarPercentageGUI = new xlvoBarPercentageGUI();
			$xlvoBarPercentageGUI->setMax($max);
			$xlvoBarPercentageGUI->setOptionLetter($xlvoOption->getCipher());
			$xlvoBarPercentageGUI->setTitle($xlvoOption->getText());
			$xlvoBarPercentageGUI->setVotes($this->manager->countVotesOfOption($xlvoOption->getId()));
			$xlvoBarPercentageGUI->setTotal($max);
			$xlvoBarPercentageGUI->setShowAbsolute($this->isShowAbsolute());

			$bars->addBar($xlvoBarPercentageGUI);
		}

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
