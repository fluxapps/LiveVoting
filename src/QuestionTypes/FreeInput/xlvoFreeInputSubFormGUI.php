<?php

namespace LiveVoting\QuestionTypes\FreeInput;

use ilCheckboxInputGUI;
use ilException;
use ilFormPropertyGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use LiveVoting\Exceptions\xlvoSubFormGUIHandleFieldException;
use LiveVoting\QuestionTypes\xlvoSubFormGUI;

/**
 * Class xlvoFreeInputSubFormGUI
 *
 * @package LiveVoting\QuestionTypes\FreeInput
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputSubFormGUI extends xlvoSubFormGUI {

	const F_MULTI_FREE_INPUT = 'multi_free_input';
	const F_ANSWER_FIELD = 'answer_field';
	const ANSWER_FIELD_SINGLE_LINE = 1;
	const ANSWER_FIELD_MULTI_LINE = 2;


	/**
	 *
	 */
	protected function initFormElements() {
		$multi_free_input = new ilCheckboxInputGUI($this->txt(self::F_MULTI_FREE_INPUT), self::F_MULTI_FREE_INPUT);
		$multi_free_input->setInfo($this->txt(self::F_MULTI_FREE_INPUT . '_info'));
		$this->addFormElement($multi_free_input);

		$answer_field = new ilRadioGroupInputGUI($this->txt(self::F_ANSWER_FIELD), self::F_ANSWER_FIELD);
		//$answer_field->setInfo($this->txt(self::F_ANSWER_FIELD . '_info'));
		$this->addFormElement($answer_field);
		$answer_field_single_line = new ilRadioOption($this->txt(self::F_ANSWER_FIELD . '_single_line'), self::ANSWER_FIELD_SINGLE_LINE);
		$answer_field_single_line->setInfo($this->txt(self::F_ANSWER_FIELD . '_single_line_info'));
		$answer_field->addOption($answer_field_single_line);
		$answer_field_multi_line = new ilRadioOption($this->txt(self::F_ANSWER_FIELD . '_multi_line'), self::ANSWER_FIELD_MULTI_LINE);
		$answer_field_multi_line->setInfo($this->txt(self::F_ANSWER_FIELD . '_multi_line_info'));
		$answer_field->addOption($answer_field_multi_line);
	}


	/**
	 * @param ilFormPropertyGUI $element
	 * @param string|array      $value
	 *
	 * @throws xlvoSubFormGUIHandleFieldException|ilException
	 */
	protected function handleField(ilFormPropertyGUI $element, $value) {
		switch ($element->getPostVar()) {
			case self::F_MULTI_FREE_INPUT:
				$this->getXlvoVoting()->setMultiFreeInput($value);
				break;
			case self::F_ANSWER_FIELD:
				$this->getXlvoVoting()->setAnswerField($value);
				break;
			default:
				throw new ilException('Unknown element can not get the value.');
		}
	}


	/**
	 * @param ilFormPropertyGUI $element
	 *
	 * @return string|int|float|array
	 * @throws ilException
	 */
	protected function getFieldValue(ilFormPropertyGUI $element) {
		switch ($element->getPostVar()) {
			case self::F_MULTI_FREE_INPUT:
				return $this->getXlvoVoting()->isMultiFreeInput();
			case self::F_ANSWER_FIELD:
				return $this->getXlvoVoting()->getAnswerField();
			default:
				throw new ilException('Unknown element can not get the value.');
				break;
		}
	}
}
