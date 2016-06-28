<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/CorrectOrder/class.xlvoCorrectOrderResultsGUI.php');

/**
 * Class xlvoFreeOrderResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeOrderResultsGUI extends xlvoCorrectOrderResultsGUI {

	/**
	 * @return string
	 */
	public function getHTML() {
		$bars = new xlvoBarCollectionGUI();

		$option_amount = count($this->manager->getOptions());
		$option_weight = array();

		foreach ($this->manager->getVotesOfVoting() as $xlvoVote) {
			$option_amount2 = $option_amount;
			foreach (json_decode($xlvoVote->getFreeInput()) as $option_id) {
				$option_weight[$option_id] = $option_weight[$option_id] + $option_amount2;
				$option_amount2 --;
			}
		}

		$total = array_sum($option_weight);
		if ($this->isShowCorrectOrder() && $this->manager->hasVotes()) {
			$unsorted_options = $this->manager->getOptions();
			$options = array();
			foreach ($option_weight as $option_id => $weight) {
				$options[] = $unsorted_options[$option_id];
			}
		} else {
			$options = $this->manager->getOptions();
		}

		foreach ($options as $xlvoOption) {
			$bars->addBar(xlvoBarPercentageGUI::getInstanceFromOption($xlvoOption, $option_weight[$xlvoOption->getId()], $total));
		}

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($this->manager->countVotes());

		return $bars->getHTML();
	}
}
