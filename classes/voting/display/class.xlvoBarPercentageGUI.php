<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarGUI.php');

class xlvoBarPercentageGUI extends xlvoBarGUI {

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
	 * @var xlvoVote[]
	 */
	protected $votes;
	/**
	 * @var string
	 */
	protected $option_letter;


	/**
	 * @param xlvoVoting $voting
	 * @param xlvoOption $option
	 * @param xlvoVote[] $votes
	 * @param            $option_letter
	 */
	public function __construct(xlvoVoting $voting, xlvoOption $option, $votes, $option_letter) {
		$this->voting = $voting;
		$this->option = $option;
		$this->votes = clone $votes;
		$this->option_letter = $option_letter;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.bar_percentage.html', true, true);
	}


	protected function render() {
		$this->tpl->setVariable('PERCENT', $this->getPercentage());
		$this->tpl->setVariable('ID', $this->option->getId());
		$this->tpl->setVariable('TITLE', $this->option->getText());
		$this->tpl->setVariable('OPTION_LETTER', $this->option_letter);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}


	protected function getPercentage() {
		$total_votes = $this->votes->count();
		$option_votes = $this->votes->where(array( 'option_id' => $this->option->getId() ))->count();
		$percentage = ($option_votes / $total_votes) * 100;

		return round($percentage, 1);
	}
}