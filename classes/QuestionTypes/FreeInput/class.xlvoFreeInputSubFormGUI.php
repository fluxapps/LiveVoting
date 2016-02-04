<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoSubFormGUI.php');

/**
 * Class xlvoFreeInputSubFormGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputSubFormGUI extends xlvoSubFormGUI {

	const F_MULTI_FREE_INPUT = 'multi_free_input';


	protected function initFormElements() {
		$cb = new ilCheckboxInputGUI($this->txt(self::F_MULTI_FREE_INPUT), self::F_MULTI_FREE_INPUT);
		//		$cb->setInfo($this->pl->txt('qtype_multi_free_input_info'));
		$this->addFormElement($cb);
	}


	/**
	 * @param ilFormPropertyGUI $element
	 * @param $value
	 * @return mixed
	 */
	protected function handleField(ilFormPropertyGUI $element, $value) {
		switch ($element->getPostVar()) {
			case self::F_MULTI_FREE_INPUT:
				$this->getXlvoVoting()->setMultiFreeInput($value);
				break;
		}
	}


	/**
	 * @param ilFormPropertyGUI $element
	 * @return mixed
	 */
	protected function getFieldValue(ilFormPropertyGUI $element) {
		switch ($element->getPostVar()) {
			case self::F_MULTI_FREE_INPUT:
				return $this->getXlvoVoting()->isMultiFreeInput();
				break;
		}
	}


	protected function handleOptions() {
		$xlvoOption = xlvoOption::where(array( 'voting_id' => $this->getXlvoVoting()->getId() ))->first();
		if (!$xlvoOption instanceof xlvoOption) {
			$xlvoOption = new xlvoOption();
			$xlvoOption->create();
		}
		$xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
		$xlvoOption->setVotingId($this->getXlvoVoting()->getId());
		$xlvoOption->setType($this->getXlvoVoting()->getVotingType());
		$xlvoOption->update();
	}
}
