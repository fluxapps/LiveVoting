<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputResultsGUI.php');

/**
 * Class xlvoFreeInputResultsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputResultsGUI extends xlvoInputResultsGUI {

	/**
	 * @return string
	 */
	public function getHTML() {

		$bars = new xlvoBarCollectionGUI();
		/**
		 * @var xlvoOption $option
		 */
		$option = $this->voting->getVotingOptions()->first();
		if (!$option instanceof xlvoOption) {
			return '';
		}
		/**
		 * @var xlvoVote[] $votes
		 */
		$votes = $this->voting_manager->getVotesOfOption($option->getId())->get();
		foreach ($votes as $vote) {
			$bars->addBar(new xlvoFreeInputResultsBarGUI($this->voting, $vote));
		}

		return $bars->getHTML();
	}
}

/**
 * Class xlvoFreeInputResultsBarGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputResultsBarGUI extends xlvoBarGUI {

	/**
	 * @var xlvoVoting
	 */
	protected $voting;
	/**
	 * @var xlvoVote
	 */
	protected $vote;


	/**
	 * @param xlvoVoting $voting
	 * @param xlvoVote $vote
	 */
	public function __construct(xlvoVoting $voting, xlvoVote $vote) {

		parent::__construct();

		$this->voting = $voting;
		$this->vote = $vote;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/tpl.bar_free_input.html', true, true);
	}


	protected function render() {
		$this->tpl->setVariable('ID', $this->vote->getId());
		$this->tpl->setVariable('FREE_INPUT', $this->vote->getFreeInput());
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}
}
