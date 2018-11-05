<?php

namespace LiveVoting\QuestionTypes\CorrectOrder;

use LiveVoting\Display\Bar\xlvoBarCollectionGUI;
use LiveVoting\Display\Bar\xlvoBarPercentageGUI;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\SingleVote\xlvoSingleVoteResultsGUI;
use xlvoCorrectOrderGUI;

/**
 * Class xlvoCorrectOrderResultsGUI
 *
 * @package LiveVoting\QuestionTypes\CorrectOrder
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoCorrectOrderResultsGUI extends xlvoSingleVoteResultsGUI {

	/**
	 * @return string
	 */
	public function getHTML() {
		$bars = new xlvoBarCollectionGUI();

		$correct_order = array();
		foreach ($this->manager->getVoting()->getVotingOptions() as $xlvoOption) {
			$correct_order[(int)$xlvoOption->getCorrectPosition()] = $xlvoOption;
			$correct_order_ids[(int)$xlvoOption->getCorrectPosition()] = $xlvoOption->getId();
		};
		ksort($correct_order);
		ksort($correct_order_ids);
		$correct_order_json = json_encode(array_values($correct_order_ids));

		$votes = $this->manager->getVotesOfVoting();
		$correct_votes = 0;
		$wrong_votes = 0;
		foreach ($votes as $xlvoVote) {
			if ($xlvoVote->getFreeInput() == $correct_order_json) {
				$correct_votes ++;
			} else {
				$wrong_votes ++;
			}
		}

		$correct_option = new xlvoOption();
		$correct_option->setText($this->txt('correct'));
		$bar = new xlvoBarPercentageGUI();
		$bar->setTitle($correct_option->getTextForPresentation());
		$bar->setVotes($correct_votes);
		$bar->setMaxVotes($this->manager->countVoters());
		$bar->setShowInPercent(!$this->isShowAbsolute());

		$bars->addBar($bar);

		$wrong_option = new xlvoOption();
		$wrong_option->setText($this->txt('wrong'));

		$bar = new xlvoBarPercentageGUI();
		$bar->setMaxVotes($this->manager->countVotes());
		$bar->setTitle($wrong_option->getTextForPresentation());
		$bar->setVotes($wrong_votes);
		$bar->setShowInPercent(!$this->isShowAbsolute());

		$bars->addBar($bar);

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($this->manager->countVotes());
		if ($this->isShowCorrectOrder()) {
			$solution_html = $this->txt('correct_solution') . '<br>';
			/**
			 * @var xlvoOption $item
			 */
			foreach ($correct_order as $item) {
				$solution_html .= ' <p><div class="xlvo-option"><span class="label label-primary xlvo-option_letter">' . $item->getCipher()
					. '</span> <span class="option_text">' . $item->getTextForPresentation() . '</span></div></p>';
			}
			$bars->addSolution($solution_html);
		}

		return $bars->getHTML();
	}


	/**
	 * @return bool
	 */
	protected function isShowCorrectOrder() {
		$states = $this->getButtonsStates();

		return ((bool)$states[xlvoCorrectOrderGUI::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER] && $this->manager->getPlayer()->isShowResults());
	}


	/**
	 * @return bool
	 */
	protected function isShowAbsolute() {
		$states = $this->getButtonsStates();

		return ($this->manager->getPlayer()->isShowResults() && (bool)$states[xlvoCorrectOrderGUI::BUTTON_TOGGLE_PERCENTAGE]);
	}
}
