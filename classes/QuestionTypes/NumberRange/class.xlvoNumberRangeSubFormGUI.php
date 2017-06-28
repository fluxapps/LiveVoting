<?php

/**
 * Class xlvoNumberRangeSubFormGUI
 *
 * This class supplies the gui for the manage view, which is used
 * to update und create new question types.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class xlvoNumberRangeSubFormGUI extends xlvoSubFormGUI{

	const OPTION_PERCENTAGE = 'option_percentage';
	const OPTION_PERCENTAGE_INFO = 'option_percentage_info';

	const OPTION_ALTERNATIVE_RESULT_DISPLAY_MODE = 'option_alternative_result_display_mode';
	const OPTION_ALTERNATIVE_RESULT_DISPLAY_MODE_INFO = 'option_alternative_result_display_mode_info';

	const OPTION_RANGE_START = 'option_range_start';
	const OPTION_RANGE_START_INFO = 'option_range_start_info';

	const OPTION_RANGE_END = 'option_range_end';
	const OPTION_RANGE_END_INFO = 'option_range_end_info';

	const START_RANGE_INVALID_INFO = 'qtype_6_invalid_start_range';
	const END_RANGE_INVALID_INFO = 'qtype_6_invalid_end_range';

	const END_RANGE_MAX = 2147483646;  //2^31 - 1       (int is always signed in php)
	const END_RANGE_MIN = -2147483647; //-2^31 + 1      (int is always signed in php)
	
	const START_RANGE_MAX = 2147483645; //2^31 - 2      (int is always signed in php)
	const START_RANGE_MIN = -2147483646; //-2^31 + 2    (int is always signed in php)


	/**
	 * Creates the GUI elements.
	 */
	protected function initFormElements() {

		//create percentage check box
		$percentageCheckBox = new \ilCheckboxInputGUI($this->txt(self::OPTION_PERCENTAGE), self::OPTION_PERCENTAGE);
		$percentageCheckBox->setInfo($this->txt(self::OPTION_PERCENTAGE_INFO));
		$percentageCheckBox->setChecked(((int)$this->getXlvoVoting()->getStartRange()) === 1);

		//create badge box option for the result display
		$alternativeResultDisplayModeCheckBox = new \ilCheckboxInputGUI($this->txt(self::OPTION_ALTERNATIVE_RESULT_DISPLAY_MODE), self::OPTION_ALTERNATIVE_RESULT_DISPLAY_MODE);
		$alternativeResultDisplayModeCheckBox->setInfo($this->txt(self::OPTION_ALTERNATIVE_RESULT_DISPLAY_MODE_INFO));
		$alternativeResultDisplayModeCheckBox->setChecked(((int)$this->getXlvoVoting()->getAltResultDisplayMode()) === 1);

		//create start range number input
		$startRange = new ilNumberInputGUI($this->txt(self::OPTION_RANGE_START), self::OPTION_RANGE_START);
		$startRange->setInfo($this->txt(self::OPTION_RANGE_START_INFO));
		$startRange->setMaxValue(self::START_RANGE_MAX);
		$startRange->setMinValue(self::START_RANGE_MIN);
		$startRange->setValue($this->getXlvoVoting()->getStartRange());
		$startRange->allowDecimals(false);

		//create end range number input
		$endRange = new ilNumberInputGUI($this->txt(self::OPTION_RANGE_END), self::OPTION_RANGE_END);
		$endRange->setInfo($this->txt(self::OPTION_RANGE_END_INFO));
		$endRange->setMaxValue(self::END_RANGE_MAX);
		$endRange->setMinValue(self::END_RANGE_MIN);
		$endRange->setValue($this->getXlvoVoting()->getEndRange());

		//add elements to gui
		$this->addFormElement($percentageCheckBox);
		$this->addFormElement($alternativeResultDisplayModeCheckBox);
		$this->addFormElement($startRange);
		$this->addFormElement($endRange);
	}


	/**
	 * Set a new value by element.
	 *
	 * @param ilFormPropertyGUI $element
	 * @param int               $value      The new value for the element. (value will be casted to int)
	 *
	 * @return \LiveVoting\Voting\xlvoVoting
	 * @throws ilException  If the element is not recognised by the handle field.
	 */
	protected function handleField(\ilFormPropertyGUI $element, $value) {
		$postKey = $element->getPostVar();
		$value = (int) $value;

		switch ($postKey)
		{
			case self::OPTION_PERCENTAGE:
				return $this->getXlvoVoting()->setPercentage($value === 1 ? 1 : 0); //if the value is 1 set 1 or else 0.
			case self::OPTION_RANGE_START:
				return $this->setStartRange($value);
			case self::OPTION_RANGE_END:
				return $this->setEndRange($value);
			case self::OPTION_ALTERNATIVE_RESULT_DISPLAY_MODE:
				return $this->getXlvoVoting()->setAltResultDisplayMode($value === 1 ? 1 : 0); //if the value is 1 set 1 or else 0.
			default:
				throw new ilException('Unknown element can not set the value.');
		}
	}


	/**
	 * Get a value by element.
	 *
	 * @param ilFormPropertyGUI $element    The element which should be used to fetch the value.
	 *
	 * @return int                          The value read with the help of the given element.
	 * @throws ilException                  Thrown if the element is not recognised.
	 */
	protected function getFieldValue(\ilFormPropertyGUI $element) {
		$postKey = $element->getPostVar();

		switch ($postKey)
		{
			case self::OPTION_PERCENTAGE:
				return (int)$this->getXlvoVoting()->getPercentage();
			case self::OPTION_RANGE_START:
				return (int)$this->getXlvoVoting()->getStartRange();
			case self::OPTION_RANGE_END:
				return (int)$this->getXlvoVoting()->getEndRange();
			case self::OPTION_ALTERNATIVE_RESULT_DISPLAY_MODE:
				return (int)$this->getXlvoVoting()->getAltResultDisplayMode();
			default:
				throw new ilException('Unknown element can not get the value.');
		}
	}


	/**
	 * Validates the start of the range and sets the value if valid.
	 *
	 * @param int $start    The new start range which should be set.
	 *
	 * @return \LiveVoting\Voting\xlvoVoting
	 */
	private function setStartRange($start)
	{
		$end = (int)$this->getXlvoVoting()->getEndRange();

		if($start < $end && $start <= self::START_RANGE_MAX && $start >= self::START_RANGE_MIN)
		{
			return $this->getXlvoVoting()->setStartRange($start);
		}

		ilUtil::sendFailure($this->pl->txt(self::START_RANGE_INVALID_INFO));
		return $this->getXlvoVoting();
	}


	/**
	 * Validates the end of the range and sets the value if valid.
	 *
	 * @param int $end  The new end range which should be set.
	 *
	 * @return \LiveVoting\Voting\xlvoVoting
	 */
	private function setEndRange($end)
	{
		$start = (int)$this->getXlvoVoting()->getStartRange();

		if($end > $start && $end <= self::END_RANGE_MAX && $end >= self::END_RANGE_MIN)
		{
			return $this->getXlvoVoting()->setEndRange($end);
		}

		ilUtil::sendFailure($this->pl->txt(self::END_RANGE_INVALID_INFO));
		return $this->getXlvoVoting();
	}
}