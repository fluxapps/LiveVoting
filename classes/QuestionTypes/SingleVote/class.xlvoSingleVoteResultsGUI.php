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
		$total_votes = $this->manager->countVotes();
		$voters = $this->manager->countVoters();

		$bars = new xlvoBarCollectionGUI();
		$bars->setShowTotalVoters(true);
		$bars->setTotalVoters($voters);
		$bars->setShowTotalVotes($this->voting->isMultiSelection());
		$bars->setTotalVotes($total_votes);

		foreach ($this->voting->getVotingOptions() as $xlvoOption) {
			$xlvoBarPercentageGUI = new xlvoBarPercentageGUI();
			$xlvoBarPercentageGUI->setMax($total_votes);
			$xlvoBarPercentageGUI->setOptionLetter($xlvoOption->getCipher());
			$xlvoBarPercentageGUI->setTitle($xlvoOption->getTextForPresentation());
			$xlvoBarPercentageGUI->setVotes($this->manager->countVotesOfOption($xlvoOption->getId()));
			$xlvoBarPercentageGUI->setTotal($total_votes);
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


	/**
	 * @param $votes xlvoVote[]
	 * @return string
	 */
	public function getTextRepresentationForVotes($votes) {
		return "TODO"; //TODO: implement me.
	}
}
