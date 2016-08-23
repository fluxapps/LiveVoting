<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarGUI.php');

/**
 * Class xlvoBarFreeInputsGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoBarFreeInputsGUI implements xlvoBarGUI {

	/**
	 * @var xlvoVoting
	 */
	protected $voting;
	/**
	 * @var xlvoVote
	 */
	protected $vote;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	/**
	 * @param xlvoVoting $voting
	 * @param xlvoVote $vote
	 */
	public function __construct(xlvoVoting $voting, xlvoVote $vote) {
		$this->voting = $voting;
		$this->vote = $vote;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_free_input.html', true, true);
	}


	protected function render() {
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
