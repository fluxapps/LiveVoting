<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoSubFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/FreeInput/class.xlvoMultiLineInputGUI.php');
/**
 * Class xlvoSingleVoteSubFormGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoSingleVoteSubFormGUI extends xlvoSubFormGUI {

	const F_MULTI_SELECTION = 'multi_selection';
	const F_OPTIONS = 'options';
	const F_TEXT = 'text';
	const F_COLORS = 'colors';
	const F_ID = 'id';
	/**
	 * @var xlvoOption[]
	 */
	protected $options = array();


	protected function initFormElements() {
		$cb = new ilCheckboxInputGUI($this->txt(self::F_MULTI_SELECTION), self::F_MULTI_SELECTION);
		//		$cb->setInfo($this->pl->txt('info_singlevote_multi_selection'));
		$this->addFormElement($cb);

		$cb = new ilCheckboxInputGUI($this->txt(self::F_COLORS), self::F_COLORS);
		//		$cb->setInfo($this->pl->txt('info_singlevote_colors'));
		//		$this->addFormElement($cb);

		$mli = new xlvoMultiLineInputGUI($this->txt(self::F_OPTIONS), self::F_OPTIONS);
		$te = new ilTextInputGUI($this->txt('option_text'), self::F_TEXT);
		$mli->addInput($te);

		$h = new ilTextInputGUI($this->txt(self::F_ID), self::F_ID);
		$h->setDisabled(true);
		$mli->addInput($h);

		$this->addFormElement($mli);
	}


	/**
	 * @param ilFormPropertyGUI $element
	 * @param $value
	 * @return mixed
	 */
	protected function handleField(ilFormPropertyGUI $element, $value) {
		switch ($element->getPostVar()) {
			case self::F_MULTI_SELECTION:
				$this->getXlvoVoting()->setMultiSelection($value);
				break;
			case self::F_OPTIONS:
				foreach ($value as $item) {
					/**
					 * @var $xlvoOption xlvoOption
					 */
					$xlvoOption = xlvoOption::findOrGetInstance($item[self::F_ID]);
					$xlvoOption->setText($item[self::F_TEXT]);
					$xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
					$xlvoOption->setVotingId($this->getXlvoVoting()->getId());
					$xlvoOption->setType($this->getXlvoVoting()->getVotingType());
					$this->options[] = $xlvoOption;
				}
				break;
			case self::F_COLORS:
				$this->getXlvoVoting()->setColors($value);
				break;
		}
	}


	/**
	 * @param ilFormPropertyGUI $element
	 * @return mixed
	 */
	protected function getFieldValue(ilFormPropertyGUI $element) {
		switch ($element->getPostVar()) {
			case self::F_MULTI_SELECTION:
				return $this->getXlvoVoting()->isMultiSelection();

			case self::F_OPTIONS:
				$array = array();
				/**
				 * @var $option xlvoOption
				 */
				$options = $this->getXlvoVoting()->getVotingOptions();
				foreach ($options as $option) {
					$array[] = array(
						self::F_ID => $option->getId(),
						self::F_TEXT => $option->getText(),
					);
				}
				return $array;

			case self::F_COLORS:
				return $this->getXlvoVoting()->isColors();
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
	}
}
