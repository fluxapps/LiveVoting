<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');

class xlvoBarOptionGUI extends xlvoBarGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;
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
	 * @param xlvoVoting $voting
	 * @param xlvoOption $option
	 * @param            $option_letter
	 */
	public function __construct(xlvoVoting $voting, xlvoOption $option, $option_letter) {

		$this->voting_manager = new xlvoVotingManager();
		$this->voting = $voting;
		$this->option = $option;
		$this->option_letter = $option_letter;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.bar_option.html', true, true);
	}


	protected function render() {
		$this->tpl->setVariable('OPTION_LETTER', $this->option_letter);
		$this->tpl->setVariable('OPTION_ID', $this->option->getId());
		$this->tpl->setVariable('TITLE', $this->option->getText());
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
		$vote = $this->voting_manager->getVotes($this->voting->getId(), $this->option->getId(), true)->first();
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
		$vote = $this->voting_manager->getVotes($this->voting->getId(), $this->option->getId(), true)->first();
		if ($vote instanceof xlvoVote) {
			return $vote->getId();
		} else {
			$no_existing_vote = 0;
			return $no_existing_vote;
		}
	}
}