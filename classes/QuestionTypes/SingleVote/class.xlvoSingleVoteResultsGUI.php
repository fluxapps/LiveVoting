<?php

use LiveVoting\Vote\xlvoVote;

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
		if ($this->voting->isMultiSelection()) {
			return $this->getHTMLMulti();
		} else {
			return $this->getHTMLSingle();
		}
	}


	/**
	 * @return string
	 */
	protected function getHTMLSingle() {
		$total_votes = $this->manager->countVotes();
		$voters = $this->manager->countVoters();

		$bars = new xlvoBarCollectionGUI();
		$bars->setShowTotalVoters(false);
		$bars->setTotalVoters($voters);
		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($voters);

		foreach ($this->voting->getVotingOptions() as $xlvoOption) {
			$xlvoBarPercentageGUI = new xlvoBarPercentageGUI();
			$xlvoBarPercentageGUI->setOptionLetter($xlvoOption->getCipher());
			$xlvoBarPercentageGUI->setTitle($xlvoOption->getTextForPresentation());
			$xlvoBarPercentageGUI->setVotes($this->manager->countVotesOfOption($xlvoOption->getId()));
			$xlvoBarPercentageGUI->setMaxVotes($total_votes);
			$xlvoBarPercentageGUI->setShowInPercent(!$this->isShowAbsolute());
			$bars->addBar($xlvoBarPercentageGUI);
		}

		return $bars->getHTML();
	}


	/**
	 * @return string
	 */
	protected function getHTMLMulti() {
		$total_votes = $this->manager->countVotes();
		$voters = $this->manager->countVoters();

		$bars = new xlvoBarCollectionGUI();
		$bars->setShowTotalVoters(false);
		$bars->setTotalVoters($voters);
		$bars->setShowTotalVotes($this->voting->isMultiSelection());
		$bars->setTotalVotes($total_votes);

		foreach ($this->voting->getVotingOptions() as $xlvoOption) {
			$xlvoBarPercentageGUI = new xlvoBarPercentageGUI();
			$xlvoBarPercentageGUI->setOptionLetter($xlvoOption->getCipher());
			$xlvoBarPercentageGUI->setTitle($xlvoOption->getTextForPresentation());
			$xlvoBarPercentageGUI->setVotes($this->manager->countVotesOfOption($xlvoOption->getId()));
			$xlvoBarPercentageGUI->setMaxVotes($voters);
			$xlvoBarPercentageGUI->setShowInPercent(!$this->isShowAbsolute());
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
