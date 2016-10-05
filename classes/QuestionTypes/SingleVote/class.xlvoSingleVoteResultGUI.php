<?php

/**
 * Class xlvoSingleVoteResultGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoSingleVoteResultGUI extends xlvoResultGUI {

	/**
	 * @param xlvoVote[] $votes
	 * @return string|xlvoOption
	 */
	public function getTextRepresentation($votes) {
		if(!count($votes))
			return "";
		$strings = array();
		foreach ($votes as $vote) {
			$strings[] = $this->options[$vote->getOptionId()]->getTextForPresentation();
		}
		return implode(", ", $strings);
	}
}