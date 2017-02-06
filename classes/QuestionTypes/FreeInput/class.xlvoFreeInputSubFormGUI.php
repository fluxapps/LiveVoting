<?php


/**
 * Class xlvoFreeInputSubFormGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputSubFormGUI extends xlvoSubFormGUI {

	const F_MULTI_FREE_INPUT = 'multi_free_input';


	protected function initFormElements() {
		$cb = new \ilCheckboxInputGUI($this->txt(self::F_MULTI_FREE_INPUT), self::F_MULTI_FREE_INPUT);
		$cb->setInfo($this->txt(self::F_MULTI_FREE_INPUT . '_info'));
		$this->addFormElement($cb);
	}


	/**
	 * @param \ilFormPropertyGUI $element
	 * @param $value
	 * @return mixed
	 */
	protected function handleField(\ilFormPropertyGUI $element, $value) {
		switch ($element->getPostVar()) {
			case self::F_MULTI_FREE_INPUT:
				$this->getXlvoVoting()->setMultiFreeInput($value);
				break;
		}
	}


	/**
	 * @param \ilFormPropertyGUI $element
	 * @return mixed
	 */
	protected function getFieldValue(\ilFormPropertyGUI $element) {
		switch ($element->getPostVar()) {
			case self::F_MULTI_FREE_INPUT:
				return $this->getXlvoVoting()->isMultiFreeInput();
				break;
		}
	}
}
