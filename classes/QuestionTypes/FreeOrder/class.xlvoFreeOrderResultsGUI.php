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

		$option_amount = $this->manager->countOptions();
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
			arsort($option_weight);
			foreach ($option_weight as $option_id => $weight) {
				$options[] = $unsorted_options[$option_id];
			}
		} else {
			$options = $this->manager->getOptions();
		}

		$absolute = $this->isShowAbsolute();

		foreach ($options as $xlvoOption) {
			$bar = new xlvoBarPercentageGUI();
			$bar->setTotal($total);
			$bar->setTitle($xlvoOption->getText());
			$bar->setId($xlvoOption->getId());
			$bar->setVotes($option_weight[$xlvoOption->getId()]);
			$bar->setMax(max($option_weight));
			$bar->setShowAbsolute($absolute);

			$bars->addBar($bar);
		}

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($this->manager->countVotes());


		return $bars->getHTML();
	}
}
