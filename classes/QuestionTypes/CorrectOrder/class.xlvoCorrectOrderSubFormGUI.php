<?php

use LiveVoting\Option\xlvoOption;

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
	const OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE = 'option_randomise_option_after_save';
	const OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE_INFO = 'option_randomise_option_after_save_info';
	/**
	 * @var xlvoOption[]
	 */
	protected $options = array();


	protected function initFormElements() {

		$xlvoMultiLineInputGUI = new xlvoMultiLineInputGUI($this->txt(self::F_OPTIONS), self::F_OPTIONS);
		$xlvoMultiLineInputGUI->setShowLabel(true);

		$randomiseOptionSequenceAfterSave = new ilCheckboxInputGUI($this->txt(self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE), self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE);
		$randomiseOptionSequenceAfterSave->setOptionTitle($this->txt(self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE_INFO));
		if ($this->getXlvoVoting()->getRandomiseOptionSequence() === 1) {
			$xlvoMultiLineInputGUI->setPositionMovable(false);
			$randomiseOptionSequenceAfterSave->setChecked(true);
		} else {
			$xlvoMultiLineInputGUI->setPositionMovable(true);
			$randomiseOptionSequenceAfterSave->setChecked(false);
		}

		$h = new \ilHiddenInputGUI(self::F_ID);
		$xlvoMultiLineInputGUI->addInput($h);

		$position = new \ilNumberInputGUI($this->txt('option_correct_position'), self::F_CORRECT_POSITION);
		$position->setSize(2);
		$position->setMaxLength(2);
		$xlvoMultiLineInputGUI->addInput($position);

		$te = new \ilTextInputGUI($this->txt('option_text'), self::F_TEXT);
		$xlvoMultiLineInputGUI->addInput($te);

		$this->addFormElement($randomiseOptionSequenceAfterSave);
		$this->addFormElement($xlvoMultiLineInputGUI);
	}


	/**
	 * @param \ilFormPropertyGUI $element
	 * @param                    $value
	 *
	 * @return void
	 * @throws ilException  If the postvar of the gui element is not recognised.
	 */
	protected function handleField(\ilFormPropertyGUI $element, $value) {
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
			case self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE:
				$value = intval($value);
				$this->getXlvoVoting()->setRandomiseOptionSequence($value === 1 ? 1 : 0);
				break;
			default:
				throw new ilException('Unknown element can not set the value.');
		}
	}


	/**
	 * @param \ilFormPropertyGUI $element
	 *
	 * @return array|int
	 */
	protected function getFieldValue(\ilFormPropertyGUI $element) {
		switch ($element->getPostVar()) {
			case self::F_OPTIONS:
				$array = [];
				/**
				 * @var $option xlvoOption
				 */
				$options = $this->getXlvoVoting()->getVotingOptions();
				foreach ($options as $option) {
					$array[] = [
						self::F_ID => $option->getId(),
						self::F_TEXT => $option->getTextForEditor(),
						self::F_POSITION => $option->getPosition(),
						self::F_CORRECT_POSITION => $option->getCorrectPosition(),
					];
				}

				return $array;
			case self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE:
				return (int)$this->getXlvoVoting()->getRandomiseOptionSequence();
		}

		return [];
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

		//randomize the order on save
		if ($this->getXlvoVoting()->getRandomiseOptionSequence() === 1) {
			$this->randomiseOptionPosition($this->options);
		}

		foreach ($this->options as $option) {
			$option->update();
		}

		$this->getXlvoVoting()->setMultiFreeInput(true);
		$this->getXlvoVoting()->update();
	}


	/**
	 * Randomises the position of the given options the position in the array is not modified at all.
	 *
	 * @param xlvoOption[] $options The options which should be randomised.
	 *
	 * @return void
	 */
	private function randomiseOptionPosition(array &$options) {

		//reorder only if there is something to reorder
		if (count($options) < 2) {
			return;
		}

		$optionsLength = count($options);
		foreach ($options as $option) {
			$newPosition = rand(1, $optionsLength);
			$previousOption = $this->findOptionByPosition($options, $newPosition);
			$previousOption->setPosition($option->getPosition());
			$option->setPosition($newPosition);
		}

		//check if we got the correct result
		if ($this->isNotCorrectlyOrdered($options)) {
			return;
		}

		//we got the right result reshuffle
		$this->randomiseOptionPosition($options);
	}


	/**
	 * Searches an option within the given option array by position.
	 *
	 * @param xlvoOption[] $options  The options which should be used to search the position.
	 * @param int          $position The position which should be searched for.
	 *
	 * @return xlvoOption
	 * @throws InvalidArgumentException Thrown if the position is not found within the given options.
	 */
	private function findOptionByPosition(array &$options, $position) {
		foreach ($options as $option) {
			if ($option->getPosition() === $position) {
				return $option;
			}
		}

		throw new InvalidArgumentException("Supplied position \"$position\" can't be found within the given options.");
	}


	/**
	 * Checks if at least one element is not correctly ordered.
	 *
	 * @param xlvoOption[] $options The options which should be checked.
	 *
	 * @return bool                     True if at least one element is not correctly ordered otherwise false.
	 */
	private function isNotCorrectlyOrdered(array &$options) {
		$incorrectOrder = 0;
		foreach ($options as $option) {
			if (strcmp($option->getCorrectPosition(), strval($option->getPosition())) !== 0) {
				$incorrectOrder ++;
			}
		}

		return $incorrectOrder > 0;
	}
}
