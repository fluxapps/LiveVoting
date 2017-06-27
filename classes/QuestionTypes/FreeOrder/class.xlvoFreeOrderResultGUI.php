<?php

/**
 * Class xlvoFreeOrderResultGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoFreeOrderResultGUI extends xlvoResultGUI {

	/**
	 * @param \LiveVoting\Vote\xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getTextRepresentation($votes) {
		$strings = array();
		if (!count($votes)) {
			return "";
		} else {
			$vote = array_shift($votes);
		}
		$json_decode = json_decode($vote->getFreeInput());
		if (!is_array($json_decode)) {
			return "";
		}
		foreach ($json_decode as $option_id) {
			$strings[] = $this->options[$option_id]->getTextForPresentation();
		}

		return implode(", ", $strings);
	}


	/**
	 * @param \LiveVoting\Vote\xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getAPIRepresentation($votes) {
		$strings = array();
		if (!count($votes)) {
			return "";
		} else {
			$vote = array_shift($votes);
		}
		$json_decode = json_decode($vote->getFreeInput());
		if (!is_array($json_decode)) {
			return "";
		}
		foreach ($json_decode as $option_id) {
			$strings[] = $this->options[$option_id]->getText();
		}

		return implode(", ", $strings);
	}
}