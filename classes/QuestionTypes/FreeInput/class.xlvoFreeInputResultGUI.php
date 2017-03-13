<?php

use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoFreeInputResultGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoFreeInputResultGUI extends xlvoResultGUI {

	/**
	 * @param xlvoVote[] $votes
	 * @return string
	 */
	public function getTextRepresentation($votes) {
		$strings = array();
		foreach ($votes as $vote) {
			$strings[] = $vote->getFreeInput();
		}

		return implode(", ", $strings);
	}


	/**
	 * @param \LiveVoting\Vote\xlvoVote[] $votes
	 * @return string
	 */
	public function getAPIRepresentation($votes) {
		return $this->getTextRepresentation($votes);
	}
}