<?php

use LiveVoting\Js\xlvoJs;
use LiveVoting\Option\xlvoOption;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoCorrectOrderGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoCorrectOrderGUI: xlvoVoter2GUI
 */
class xlvoCorrectOrderGUI extends xlvoQuestionTypesGUI {

	const BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER = 'display_correct_order';
	const BUTTON_TOGGLE_PERCENTAGE = 'toggle_percentage';

	private $randomizeOptions = true;


	/**
	 * @return string
	 */
	public function getMobileHTML() {
		return $this->getFormContent();
	}


	public function initJS() {
		xlvoJs::getInstance()->api($this)->name('CorrectOrder')->category('QuestionTypes')->addLibToHeader('jquery.ui.touch-punch.min.js')->init();
	}


	protected function submit() {
		$this->manager->inputOne(array(
			"input" => json_encode($_POST['id']),
			"vote_id" => $_POST['vote_id']
		));
	}


	protected function clear() {
		$this->manager->unvoteAll();
		$this->afterSubmit();
	}


	/**
	 * @return string
	 */
	protected function getFormContent() {
		$pl = ilLiveVotingPlugin::getInstance();

		$tpl = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/QuestionTypes/FreeOrder/tpl.free_order.html', true, false);
		$tpl->setVariable('ACTION', $this->ctrl->getFormAction($this));
		$tpl->setVariable('ID', 'xlvo_sortable');
		$tpl->setVariable('BTN_RESET', $pl->txt('qtype_4_clear'));
		$tpl->setVariable('BTN_SAVE', $pl->txt('qtype_4_save'));

		$vote = array_shift(array_values($this->manager->getVotesOfUser()));
		$order = array();
		$vote_id = null;
		if ($vote instanceof xlvoVote) {
			$order = json_decode($vote->getFreeInput());
			$vote_id = $vote->getId();
		}
		if (!$vote_id) {
			$tpl->setVariable('BTN_RESET_DISABLED', 'disabled="disabled"');
		}

		$options = NULL;

		if($this->randomizeOptions) {
			//randomize the options for the voters
			$options = $this->randomizeWithoutCorrectSequence(
				$this->manager->getVoting()->getVotingOptions()
			);
		}
		else {
			$options = $this->manager->getVoting()->getVotingOptions();
		}


		$bars = new xlvoBarMovableGUI($options, $order, $vote_id);
		$bars->setShowOptionLetter(true);
		$tpl->setVariable('CONTENT', $bars->getHTML());

		if ($this->isShowCorrectOrder()) {
			$correct_order = $this->getCorrectOrder();
			$solution_html = $this->txt('correct_solution');

			foreach ($correct_order as $item) {
				$solution_html .= ' <span class="label label-primary">' . $item->getCipher() . '</span>';
			}

			$tpl->setVariable('YOUR_SOLUTION', $solution_html);
		}

		return $tpl->get();
	}


	/**
	 * @return array
	 */
	public function getButtonInstances() {
		if (!$this->manager->getPlayer()->isShowResults()) {
			return array();
		}
		$states = $this->getButtonsStates();
		$b = \ilLinkButton::getInstance();
		$b->setId(self::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER);
		if ($states[self::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER]) {
			$b->setCaption(xlvoGlyphGUI::get('eye-close'), false);
		} else {
			$b->setCaption(xlvoGlyphGUI::get('eye-open'), false);
		}

		$t = \ilLinkButton::getInstance();
		$t->setId(self::BUTTON_TOGGLE_PERCENTAGE);
		if ($states[self::BUTTON_TOGGLE_PERCENTAGE]) {
			$t->setCaption('%', false);
		} else {
			$t->setCaption(xlvoGlyphGUI::get('user'), false);
		}

		return array( $b, $t );
	}


	/**
	 * @return mixed
	 */
	protected function isShowCorrectOrder() {
		$states = $this->getButtonsStates();

		return ((bool)$states[xlvoCorrectOrderGUI::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER] && $this->manager->getPlayer()->isShowResults());
	}


	/**
	 * @param $button_id
	 * @param $data
	 */
	public function handleButtonCall($button_id, $data) {
		$states = $this->getButtonsStates();
		$this->saveButtonState($button_id, !$states[$button_id]);
	}


	/**
	 * Checks whether the options displayed to the voter is randomized.
	 * The options get randomized by default.
	 *
	 * @return bool
	 */
	public function isRandomizeOptions() {
		return $this->randomizeOptions;
	}


	/**
	 * Set the configuration regarding the randomization of the options.
	 *
	 * @param bool $randomizeOptions
	 */
	public function setRandomizeOptions($randomizeOptions) {
		$this->randomizeOptions = $randomizeOptions;
	}



	/**
	 * @return xlvoOption[]
	 */
	protected function getCorrectOrder() {
		$correct_order = array();
		foreach ($this->manager->getVoting()->getVotingOptions() as $xlvoOption) {
			$correct_order[(int)$xlvoOption->getCorrectPosition()] = $xlvoOption;
		};
		ksort($correct_order);
		return $correct_order;
	}


	/**
	 * Randomizes an array of xlvoOption.
	 * This function never returns the correct sequence of options.
	 *
	 * @param xlvoOption[] $options The options which should get randomized.
	 *
	 * @return xlvoOption[] The randomized option array.
	 */
	private function randomizeWithoutCorrectSequence(array &$options)
	{
		if(count($options) <= 1)
			return $options;

		//shuffle array items (can't use the PHP shuffle function because the keys are not preserved.)
		$optionsClone = $this->shuffleArray($options);

		$lastCorrectPosition = 0;

		/**
		 * @var xlvoOption $option
		 */
		foreach ($optionsClone as $option)
		{
			//get correct item position
			$currentCurrentPosition = $option->getCorrectPosition();

			//calculate the difference
			$difference = $lastCorrectPosition - $currentCurrentPosition;
			$lastCorrectPosition = $currentCurrentPosition;

			//check if we shuffled the correct answer by accident.
			//the correct answer would always produce a difference of -1.
			//1 - 2 = -1, 2 - 3 = -1, 3 - 4 = -1 ...
			if($difference !== -1)
				return $optionsClone;
		}

		//try to shuffle again because we got the right answer by accident.
		//we pass the original array, this should enable php to drop the array clone out of the memory.
		return $this->randomizeWithoutCorrectSequence($options);
	}


	/**
	 * Shuffles the array given array the keys are preserved.
	 * Please note that the array passed into this method get never modified.
	 *
	 * @param array $array  The array which should be shuffled.
	 *
	 * @return array The newly shuffled array.
	 */
	private function shuffleArray(array &$array)
	{
		$clone = $this->cloneArray($array);
		$shuffledArray = [];

		while(count($clone) > 0)
		{
			$key = array_rand($clone);
			$shuffledArray[$key] = &$clone[$key];
			unset($clone[$key]);
		}

		return $shuffledArray;
	}


	/**
	 * Create a shallow copy of the given array.
	 *
	 * @param array $array  The array which should be copied.
	 *
	 * @return array    The newly created shallow copy of the given array.
	 */
	private function cloneArray(array &$array)
	{
		$clone = [];
		foreach($array as $key => $value)
		{
			$clone[$key] = &$array[$key]; //get the ref on the array value not the foreach value.
		}

		return $clone;
	}
}
