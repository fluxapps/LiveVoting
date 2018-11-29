<?php

namespace LiveVoting\QuestionTypes\NumberRange;

use LiveVoting\QuestionTypes\xlvoResultGUI;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoNumberRangeResultGUI
 *
 * @package LiveVoting\QuestionTypes\NumberRange
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class xlvoNumberRangeResultGUI extends xlvoResultGUI {

	/**
	 * @param xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getTextRepresentation(array $votes) {
		return $this->createCSV($votes);
	}


	/**
	 * @param xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getAPIRepresentation(array $votes) {
		return $this->createCSV($votes);
	}


	/**
	 * @param array $votes The votes which should be used to create the csv string.
	 *
	 * @return string
	 */
	private function createCSV(array $votes) {
		$testVotes = [];

		foreach ($votes as $vote) {
			$percentage = (int)$this->voting->getPercentage() === 1 ? ' %' : '';
			$testVotes[] = "{$vote->getFreeInput()}{$percentage}";
		}

		return implode(', ', $testVotes);
	}
}
