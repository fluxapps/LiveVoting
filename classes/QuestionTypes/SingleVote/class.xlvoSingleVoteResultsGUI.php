<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputResultsGUI.php');

/**
 * Class xlvoSingleVoteResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoSingleVoteResultsGUI extends xlvoInputResultsGUI {

	/**
	 * @var int
	 */
	protected $answer_count = 64;
	/**
	 * @var xlvoOption[]
	 */
	protected $options = array();


	/**
	 * xlvoFreeInputDisplayGUI constructor.
	 * @param xlvoVoting $voting
	 * @param xlvoVotingManager $voting_manager
	 */
	public function __construct(xlvoVoting $voting, xlvoVotingManager $voting_manager) {
		parent::__construct($voting, $voting_manager);
		/**
		 * @var xlvoOption[] $options
		 */
		$this->options = $this->voting->getVotingOptions();
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		/**
		 * @var xlvoBarCollectionGUI
		 */
		$bars = new xlvoBarCollectionGUI();
		foreach ($this->options as $option) {
			$this->answer_count ++;
			/**
			 * @var xlvoVote $votes
			 */
			$votes = $this->voting_manager->getVotesOfVoting($this->voting->getId());

			$bars->addBar(new xlvoBarPercentageGUI($this->voting, $option, $votes, chr($this->answer_count)));
		}

		return $bars->getHTML();
	}
}
