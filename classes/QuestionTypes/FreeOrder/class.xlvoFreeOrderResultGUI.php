<?php

/**
 * Class xlvoFreeOrderResultGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoFreeOrderResultGUI extends xlvoResultGUI {

	public function getTextRepresentation($votes) {
		$strings = array();
		if(!count($votes))
			return "";
		else
			$vote = array_shift($votes);
		foreach (json_decode($vote->getFreeInput()) as $option_id) {
			$strings[] = $this->options[$option_id]->getTextForPresentation();
		}
		return implode(", ", $strings);
	}
}