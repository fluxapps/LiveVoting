<?php

use LiveVoting\Option\xlvoOption;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoSingleVoteResultGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoSingleVoteResultGUI extends xlvoResultGUI {

	/**
	 * @param xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getTextRepresentation(array $votes) {
		if (!count($votes)) {
			return "";
		}
		$strings = array();
		foreach ($votes as $vote) {
			$xlvoOption = $this->options[$vote->getOptionId()];
			if ($xlvoOption instanceof xlvoOption) {
				$strings[] = $xlvoOption->getTextForPresentation();
			} else {
				$strings[] = $this->pl->txt("common_option_no_longer_available");
			}
		}

		return implode(", ", $strings);
	}


	/**
	 * @param xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getAPIRepresentation(array $votes) {
		if (!count($votes)) {
			return "";
		}
		$strings = array();
		foreach ($votes as $vote) {
			$strings[] = $this->options[$vote->getOptionId()]->getText();
		}

		return implode(", ", $strings);
	}
}
