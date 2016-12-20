<?php

use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoCorrectOrderResultGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoCorrectOrderResultGUI extends xlvoResultGUI{

	/**
	 * @param xlvoVote[] $votes
	 * @return string
	 */
	public function getTextRepresentation($votes) {
		$strings = array();
		if(!count($votes))
			return "";
		else
			$vote = array_shift($votes);

		$correct_order_ids = array();
		foreach ($this->options as $option) {
			$correct_order_ids[(int)$option->getCorrectPosition()] = $option->getId();
		};
		ksort($correct_order_ids);
		$correct_order_json = json_encode(array_values($correct_order_ids));
		$return = ($correct_order_json == $vote->getFreeInput())?$this->pl->txt("common_correct_order"):$this->pl->txt("common_incorrect_order");
		$return .= ": ";
		foreach (json_decode($vote->getFreeInput()) as $option_id) {
			$strings[] = $this->options[$option_id]->getTextForPresentation();
		}
		return $return.implode(", ", $strings);
	}
}