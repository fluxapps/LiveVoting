<?php

namespace LiveVoting\QuestionTypes\FreeInput;

use ilCronManager;
use LiveVoting\Display\Bar\xlvoBarFreeInputsGUI;
use LiveVoting\Display\Bar\xlvoBarGroupingCollectionGUI;
use LiveVoting\Option\xlvoOption;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVotingManager2;

/**
 * Class xlvoFreeInputCategorizeGUI
 *
 * @package LiveVoting\QuestionTypes\FreeInput
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class xlvoFreeInputCategorizeGUI {

	/**
	 * @var xlvoVotingManager2
	 */
	protected $manager;

	/**
	 * xlvoFreeInputCategorizeGUI constructor.
	 */
	public function __construct($xlvoManager) {
		$this->manager = $xlvoManager;
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$bars = new xlvoBarGroupingCollectionGUI();
		$bars->setShowTotalVotes(true);

		/**
		 * @var xlvoOption $option
		 */
		$option = $this->manager->getVoting()->getFirstVotingOption();

		/**
		 * @var xlvoVote[] $votes
		 */
		$votes = $this->manager->getVotesOfOption($option->getId());
		foreach ($votes as $vote) {
			$bars->addBar(new xlvoBarFreeInputsGUI($this->manager->getVoting(), $vote));
		}
		$bars->setTotalVotes(count($votes));

		return $bars->getHTML();
	}
}