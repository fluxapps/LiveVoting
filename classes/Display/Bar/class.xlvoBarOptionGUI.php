<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingManager.php');

/**
 * Class xlvoBarOptionGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoBarOptionGUI implements xlvoBarGUI {

	/**
	 * @var xlvoVoting
	 */
	protected $voting;
	/**
	 * @var xlvoOption
	 */
	protected $option;
	/**
	 * @var string
	 */
	protected $option_letter;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var xlvoVotingManager
	 */
	protected $voting_manager;


	/**
	 * @param xlvoVoting $voting
	 * @param xlvoOption $option
	 * @param            $option_letter
	 */
	public function __construct(xlvoVoting $voting, xlvoOption $option, $option_letter) {
		$this->voting_manager = new xlvoVotingManager();
		$this->voting = $voting;
		$this->option = $option;
		$this->option_letter = $option_letter;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_option.html', true, true);
	}


	protected function render() {
		$this->tpl->setVariable('OPTION_LETTER', $this->option_letter);
		$this->tpl->setVariable('OPTION_ID', $this->option->getId());
		$this->tpl->setVariable('TITLE', $this->option->getTextForPresentation());
		$this->tpl->setVariable('OPTION_ACTIVE', $this->getActiveBar());
		$this->tpl->setVariable('VOTE_ID', $this->getVoteId());
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}


	private function getActiveBar() {
		/**
		 * @var $vote xlvoVote
		 */
		$vote = $this->voting_manager->getVotesOfUserOfOption($this->voting->getId(), $this->option->getId())->first();
		if ($vote instanceof xlvoVote) {
			if ($vote->getStatus() == 1) {
				return "active";
			} else {
				return "";
			}
		} else {
			return "";
		}
	}


	private function getVoteId() {
		/**
		 * @var $vote xlvoVote
		 */
		$vote = $this->voting_manager->getVotesOfUserOfOption($this->voting->getId(), $this->option->getId())->first();
		if ($vote instanceof xlvoVote) {
			return $vote->getId();
		} else {
			$no_existing_vote = 0;

			return $no_existing_vote;
		}
	}
}