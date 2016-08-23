<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoSubFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/FreeInput/class.xlvoMultiLineInputGUI.php');

/**
 * Class xlvoCorrectOrderSubFormGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoCorrectOrderSubFormGUI extends xlvoSubFormGUI {

	const F_OPTIONS = 'options';
	const F_TEXT = 'text';
	const F_ID = 'id';
	const F_POSITION = 'position';
	const F_CORRECT_POSITION = 'correct_position';
	/**
	 * @var xlvoOption[]
	 */
	protected $options = array();


	protected function initFormElements() {
		$xlvoMultiLineInputGUI = new xlvoMultiLineInputGUI($this->txt(self::F_OPTIONS), self::F_OPTIONS);
		$xlvoMultiLineInputGUI->setShowLabel(true);
		$xlvoMultiLineInputGUI->setPositionMovable(true);

		$h = new ilHiddenInputGUI(self::F_ID);
		$xlvoMultiLineInputGUI->addInput($h);

		$position = new ilNumberInputGUI($this->txt('option_correct_position'), self::F_CORRECT_POSITION);
		$position->setSize(2);
		$position->setMaxLength(2);
		$xlvoMultiLineInputGUI->addInput($position);

		$te = new ilTextInputGUI($this->txt('option_text'), self::F_TEXT);
		$xlvoMultiLineInputGUI->addInput($te);

		$this->addFormElement($xlvoMultiLineInputGUI);
	}


	/**
	 * @param ilFormPropertyGUI $element
	 * @param $value
	 * @return mixed
	 */
	protected function handleField(ilFormPropertyGUI $element, $value) {
		switch ($element->getPostVar()) {
			case self::F_OPTIONS:
				$pos = 1;
				foreach ($value as $item) {
					/**
					 * @var $xlvoOption xlvoOption
					 */
					$xlvoOption = xlvoOption::findOrGetInstance($item[self::F_ID]);
					$xlvoOption->setText($item[self::F_TEXT]);
					$xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
					$xlvoOption->setVotingId($this->getXlvoVoting()->getId());
					$xlvoOption->setPosition($pos);
					$xlvoOption->setCorrectPosition($item[self::F_CORRECT_POSITION]);
					$xlvoOption->setType($this->getXlvoVoting()->getVotingType());
					$this->options[] = $xlvoOption;
					$pos ++;
				}
				break;
		}
	}


	/**
	 * @param ilFormPropertyGUI $element
	 * @return mixed
	 */
	protected function getFieldValue(ilFormPropertyGUI $element) {
		switch ($element->getPostVar()) {
			case self::F_OPTIONS:
				$array = array();
				/**
				 * @var $option xlvoOption
				 */
				$options = $this->getXlvoVoting()->getVotingOptions();
				foreach ($options as $option) {
					$array[] = array(
						self::F_ID               => $option->getId(),
						self::F_TEXT             => $option->getText(),
						self::F_POSITION         => $option->getPosition(),
						self::F_CORRECT_POSITION => $option->getCorrectPosition(),
					);
				}

				return $array;
		}
	}


	protected function handleOptions() {
		$ids = array();
		foreach ($this->options as $xlvoOption) {
			$xlvoOption->setVotingId($this->getXlvoVoting()->getId());
			$xlvoOption->store();
			$ids[] = $xlvoOption->getId();
		}
		$options = $this->getXlvoVoting()->getVotingOptions();

		foreach ($options as $xlvoOption) {
			if (!in_array($xlvoOption->getId(), $ids)) {
				$xlvoOption->delete();
			}
		}
		$this->getXlvoVoting()->setMultiFreeInput(true);
		$this->getXlvoVoting()->renegerateOptionSorting();
		$this->getXlvoVoting()->update();
	}
}
