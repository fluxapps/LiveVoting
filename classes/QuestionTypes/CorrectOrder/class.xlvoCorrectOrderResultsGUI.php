<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/SingleVote/class.xlvoSingleVoteResultsGUI.php');
require_once('class.xlvoCorrectOrderGUI.php');

/**
 * Class xlvoCorrectOrderResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
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

		$wrong_option = new xlvoOption();
		$wrong_option->setText($this->txt('wrong'));

		$bars->addBar(xlvoBarPercentageGUI::getInstanceFromOption($correct_option, $correct_votes, count($votes)));
		$bars->addBar(xlvoBarPercentageGUI::getInstanceFromOption($wrong_option, $wrong_votes, count($votes)));

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($this->manager->countVotes());
		if ($this->isShowCorrectOrder()) {
			$solution_html = $this->txt('correct_solution');
			/**
			 * @var $item xlvoOption
			 */
			foreach ($correct_order as $item) {
				$solution_html .= ' <h1 class="xlvo-option"><span class="label label-primary xlvo-option">' . $item->getCipher() . '</span></h1>';
			}
			$bars->addSolution($solution_html);
		}

		return $bars->getHTML();
	}


	/**
	 * @return mixed
	 */
	protected function isShowCorrectOrder() {
		$states = $this->getButtonsStates();

		return (bool)$states[xlvoCorrectOrderGUI::BUTTON_DISPLAY_CORRECT_ORDER];
	}
}
