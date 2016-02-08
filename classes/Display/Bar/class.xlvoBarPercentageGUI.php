<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarGUI.php');

/**
 * Class xlvoBarPercentageGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoBarPercentageGUI implements xlvoBarGUI {

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
	 * @var ilTemplate
	 */
	protected $tpl;


	/**
	 * @param xlvoVoting $voting
	 * @param xlvoOption $option
	 * @param            $votes
	 * @param            $option_letter
	 */
	public function __construct(xlvoVoting $voting, xlvoOption $option, $votes, $option_letter) {
		$this->voting = $voting;
		$this->option = $option;
		$this->votes = $votes;
		$this->option_letter = $option_letter;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/tpl.bar_percentage.html', true, true);
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


	/**
	 * @return float|int
	 */
	protected function getPercentage() {
		$total_votes = count($this->votes);
		if ($total_votes === 0) {
			return 0;
		}
		$option_votes = xlvoVote::where(array(
			'voting_id' => $this->voting->getId(),
			'status' => xlvoVote::STAT_ACTIVE,
			'option_id' => $this->option->getId()
		))->count(); // TODO REFACTOR
		$percentage = ($option_votes / $total_votes) * 100;

		return round($percentage, 1);
	}
}