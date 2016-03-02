<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/SingleVote/class.xlvoSingleVoteResultsGUI.php');

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
			$correct_order[(int)$xlvoOption->getCorrectPosition()] = $xlvoOption->getId();
		};
		ksort($correct_order);
		$correct_order = json_encode(array_values($correct_order));

		$votes = $this->manager->getVotesOfVoting();
		$correct_votes = 0;
		$wrong_votes = 0;
		foreach ($votes as $xlvoVote) {
			if($xlvoVote->getFreeInput() == $correct_order) {
				$correct_votes++;
			}else {
				$wrong_votes++;
			}
		}

		$correct_option = new xlvoOption();
		$correct_option->setText('Richtig');

		$wrong_option = new xlvoOption();
		$wrong_option->setText('Falsch');

		$bars->addBar(xlvoBarPercentageGUI::getInstanceFromOption($correct_option, $correct_votes, count($votes)));
		$bars->addBar(xlvoBarPercentageGUI::getInstanceFromOption($wrong_option, $wrong_votes, count($votes)));

		$bars->setShowTotalVotes(true);
		$bars->setTotalVotes($this->manager->countVotes());

		return $bars->getHTML();
	}
}
