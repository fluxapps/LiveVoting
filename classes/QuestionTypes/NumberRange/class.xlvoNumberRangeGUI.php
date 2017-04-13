<?php
use LiveVoting\Js\xlvoJs;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoNumberRange
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoNumberRangeGUI: xlvoVoter2GUI
 */
class xlvoNumberRangeGUI extends xlvoQuestionTypesGUI{

	const USER_SELECTED_NUMBER = 'user_selected_number';
	const SAVE_BUTTON_VOTE = 'voter_start_button_vote';
	const SAVE_BUTTON_UNVOTE = 'voter_start_button_unvote';
	const SLIDER_STEP = 1;

	private $option;


	public function setManager($manager) {

		if($manager === NULL)
			throw new ilException('The manager must not be null.');

		parent::setManager($manager);

		//fetch the first option
		$this->option = array_values($this->manager->getOptions())[0];

		if($this->option === NULL)
			throw new ilException('No option found for the number range question.');
	}

	public function initJS() {
		xlvoJs::getInstance()->api($this)
			->name('NumberRange')
			->category('QuestionTypes')
			->addLibToHeader('bootstrap-slider.min.js')
			->addSettings(['percentage' => (int)$this->manager->getVoting()->getPercentage() === 1])
			->init();
	}


	protected function submit() {

		if($this->manager === NULL)
			throw new ilException('The NumberRange question got no voting manager! Please set one via setManager.');

		//vote
		//the vote method also unvotes because of that we need some checks, if we unvoted or not.
		$this->manager->vote($this->option->getId());

		//get all votes of the currents user
		$votes = $this->manager->getVotesOfUser(false);

		//check if we voted or unvoted
		if(count($votes) === 1)
		{
			//we voted
			$vote = array_values($votes)[0];

			//filter the input and convert to int
			$filteredInput = filter_input(INPUT_POST, self::USER_SELECTED_NUMBER, FILTER_VALIDATE_INT);

			//check if the filter failed
			if($filteredInput !== false && $filteredInput !== NULL)
			{
				//filter succeeded set value and store vote
				$start = (int)$this->manager->getVoting()->getStartRange();
				$end = (int)$this->manager->getVoting()->getEndRange();

				//validate user input
				if($this->isVoteValid($start, $end, $filteredInput))
				{
					$vote->setFreeInput($filteredInput);
					$vote->store();
					return;
				}
			}

			//the filter failed or the user supplied an invalid value.
			$this->manager->unvote($this->option->getId());
		}

	}


	public function getMobileHTML() {

		$start = (int)$this->manager->getVoting()->getStartRange();
		$end = (int)$this->manager->getVoting()->getEndRange();
		$sliderValue = ceil(($start + $end) / 2);

		$template = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/QuestionTypes/NumberRange/tpl.number_range.html', true, true);
		$template->setVariable('ACTION', $this->ctrl->getFormAction($this));
		$template->setVariable('SLIDER_MIN', $start);
		$template->setVariable('SLIDER_MAX', $end);
		$template->setVariable('SLIDER_STEP', self::SLIDER_STEP);

		$userVotes = $this->manager->getVotesOfUser(false);
		if(count($userVotes) > 0)
		{
			/**
			 * @var xlvoVote[] $userVotes
			 */
			$userVotes = array_values($userVotes);
			$template->setVariable('SLIDER_VALUE', (int)$userVotes[0]->getFreeInput());
			$template->setVariable('BTN_SAVE', $this->txt(self::SAVE_BUTTON_UNVOTE));
		}
		else{
			$template->setVariable('SLIDER_VALUE', $sliderValue);
			$template->setVariable('BTN_SAVE', $this->txt(self::SAVE_BUTTON_VOTE));
		}

		return $template->get();
	}

	private function isVoteValid($start, $end, $value)
	{
		return $value >= $start && $value <= $end;
	}
}