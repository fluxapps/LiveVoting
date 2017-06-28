<?php
use LiveVoting\Js\xlvoJs;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoNumberRange
 *
 * @author            Nicolas Schäfli <ns@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoNumberRangeGUI: xlvoVoter2GUI
 */
class xlvoNumberRangeGUI extends xlvoQuestionTypesGUI {

	const USER_SELECTED_NUMBER = 'user_selected_number';
	const SAVE_BUTTON_VOTE = 'voter_start_button_vote';
	const CLEAR_BUTTON = 'voter_clear';
	const SAVE_BUTTON_UNVOTE = 'voter_start_button_unvote';
	const SLIDER_STEP = 1;


	public function setManager($manager) {

		if ($manager === null) {
			throw new ilException('The manager must not be null.');
		}

		parent::setManager($manager);
	}


	public function initJS() {
		xlvoJs::getInstance()->api($this)->name('NumberRange')->category('QuestionTypes')->addLibToHeader('bootstrap-slider.min.js')->init();
	}


	protected function clear() {
		$this->manager->unvoteAll();
		$this->afterSubmit();
	}


	protected function submit() {

		if ($this->manager === null) {
			throw new ilException('The NumberRange question got no voting manager! Please set one via setManager.');
		}

		//get all votes of the currents user
		$votes = $this->manager->getVotesOfUser(false);

		//check if we voted or unvoted

		//we voted

		//filter the input and convert to int
		$filteredInput = filter_input(INPUT_POST, self::USER_SELECTED_NUMBER, FILTER_VALIDATE_INT);

		//check if the filter failed
		if ($filteredInput !== false && $filteredInput !== null) {
			//filter succeeded set value and store vote
			$start = (int)$this->manager->getVoting()->getStartRange();
			$end = (int)$this->manager->getVoting()->getEndRange();

			//validate user input
			if ($this->isVoteValid($start, $end, $filteredInput)) {
				//vote
				$this->manager->inputOne([
					'input'   => $filteredInput,
					'vote_id' => '-1',
				]);

				return;
			}
		}
	}


	public function getMobileHTML() {
		$start = (int)$this->manager->getVoting()->getStartRange();
		$end = (int)$this->manager->getVoting()->getEndRange();
		$sliderValue = ceil(($start + $end) / 2);

		$template = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/QuestionTypes/NumberRange/tpl.number_range.html', true, true);
		$template->setVariable('ACTION', $this->ctrl->getFormAction($this));
		$template->setVariable('SHOW_PERCENTAGE', (int)$this->manager->getVoting()->getPercentage());

		$userVotes = $this->manager->getVotesOfUser(false);
		$userVotes = array_values($userVotes);
		/**
		 * @var xlvoVote[] $userVotes
		 */

		$template->setVariable('SLIDER_MIN', $start);
		$template->setVariable('SLIDER_MAX', $end);
		$template->setVariable('SLIDER_STEP', self::SLIDER_STEP);
		if ($userVotes[0] instanceof xlvoVote) {
			$user_has_voted = true;
			$value = (int)$userVotes[0]->getFreeInput();
		} else {
			$user_has_voted = false;
			$value = $sliderValue;
		}
		$template->setVariable('SLIDER_VALUE', $value);
		$template->setVariable('BTN_SAVE', $this->txt(self::SAVE_BUTTON_VOTE));
		$template->setVariable('BTN_CLEAR', $this->txt(self::CLEAR_BUTTON));

		if (!$user_has_voted) {
			$template->setVariable('BTN_RESET_DISABLED', 'disabled="disabled"');
		}

		return $template->get();
	}


	private function isVoteValid($start, $end, $value) {
		return $value >= $start && $value <= $end;
	}
}