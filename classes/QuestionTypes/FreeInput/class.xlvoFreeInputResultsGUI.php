<?php

use LiveVoting\Option\xlvoOption;
use LiveVoting\Vote\xlvoVote;

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
		$bars = new xlvoBarGroupingCollectionGUI();
		$bars->setShowTotalVotes(true);

		/**
		 * @var xlvoOption $option
		 */
		$option = $this->manager->getVoting()->getFirstVotingOption();

		/**
		 * @var xlvoVote[] $votes
		 */
		$votes = $this->manager->getVotesOfOption($option->getId());
		foreach ($votes as $vote) {
			$bars->addBar(new xlvoBarFreeInputsGUI($this->manager->getVoting(), $vote));
		}
		$bars->setTotalVotes(count($votes));

		return $bars->getHTML();
	}


	/**
	 * @param $votes xlvoVote[]
	 *
	 * @return string
	 */
	public function getTextRepresentationForVotes($votes) {
		$string_votes = array();
		foreach ($votes as $vote) {
			$string_votes[] = $vote->getFreeInput();
		}

		return implode(", ", $string_votes);
	}
}
