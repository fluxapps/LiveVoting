<?php

namespace LiveVoting\QuestionTypes\FreeInput;

use LiveVoting\Display\Bar\xlvoBarFreeInputsGUI;
use LiveVoting\Display\Bar\xlvoBarGroupingCollectionGUI;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoInputResultsGUI;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoFreeInputResultsGUI
 *
 * @package LiveVoting\QuestionTypes\FreeInput
 * @author  Fabian Schmid <fs@studer-raimann.ch>
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
	 * @param xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getTextRepresentationForVotes(array $votes) {
		$string_votes = array();
		foreach ($votes as $vote) {
			$string_votes[] = str_replace([ "\r\n", "\r", "\n" ], " ", $vote->getFreeInput());
		}

		return implode(", ", $string_votes);
	}
}
