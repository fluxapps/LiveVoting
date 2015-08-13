<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarGUI.php');

class xlvoBarFreeInputGUI extends xlvoBarGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;
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
	 * @param xlvoVote   $vote
	 */
	public function __construct(xlvoVoting $voting, xlvoVote $vote) {
		$this->voting = $voting;
		$this->vote = $vote;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.bar_free_input.html', false, false);
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