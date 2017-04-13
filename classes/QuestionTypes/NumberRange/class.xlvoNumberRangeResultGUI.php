<?php

/**
 * Class xlvoNumberRangeResultGUI
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class xlvoNumberRangeResultGUI extends xlvoResultGUI{

	/**
	 * Creates a text representation from the given votes.
	 * This method is used by the history object to create a readable history.
	 *
	 * @param \LiveVoting\Vote\xlvoVote[] $votes    The votes which should be transformed into a text representation.
	 *
	 * @return string
	 */
	public function getTextRepresentation($votes) {
		return $this->createCSV($votes);
	}


	public function getAPIRepresentation($votes) {

		return $this->createCSV($votes);
	}


	/**
	 * @param array $votes  The votes which should be used to create the csv string.
	 *
	 * @return string
	 */
	private function createCSV(array $votes)
	{
		$testVotes = [];

		foreach ($votes as $vote)
		{
			$percentage = (int)$this->voting->getPercentage() === 1 ? '%' : '';
			$testVotes[] = "{$vote->getFreeInput()}{$percentage}";
		}
		return implode(', ', $testVotes);
	}
}