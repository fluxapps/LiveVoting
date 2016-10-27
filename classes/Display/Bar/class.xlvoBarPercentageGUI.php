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
	 * @var int
	 */
	protected $votes = 0;
	/**
	 * @var int
	 */
	protected $total = 0;
	/**
	 * @var string
	 */
	protected $option_letter;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var string
	 */
	protected $title = '';
	/**
	 * @var string
	 */
	protected $id = '';
	/**
	 * @var bool
	 */
	protected $show_absolute = false;
	/**
	 * @var bool
	 */
	protected $override_bar_to_percentage = false;
	/**
	 * @var int
	 */
	protected $max = 100;
	/**
	 * @var int
	 */
	protected $round_positions = 2;
	/**
	 * @var bool
	 */
	protected $round = false;


	/**
	 * xlvoBarPercentageGUI constructor.
	 */
	public function __construct() {
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_percentage.html', true, true);
	}


	protected function render() {
		if (!$this->isShowAbsolute() || $this->isOverrideBarToPercentage()) {
			$this->setMax(100);
			$this->tpl->setVariable('PERCENT', $this->getPercentage());
			$this->tpl->setVariable('PERCENT_TEXT', $this->getPercentage() . '%');
			$this->tpl->setVariable('PERCENT_STYLE', $this->getPercentage());
		} elseif ($this->isShowAbsolute()) {
			$this->tpl->setVariable('PERCENT', $this->getVotes());
			if ($this->isRound()) {
				$this->tpl->setVariable('PERCENT_TEXT', round($this->getVotes(), $this->getRoundPositions()));
			} else {
				$this->tpl->setVariable('PERCENT_TEXT', $this->getVotes());
			}

			$this->tpl->setVariable('PERCENT_STYLE', $this->getAbsolutePercentage());
		}

		if ($this->isShowAbsolute()) {
			if ($this->isRound()) {
				$this->tpl->setVariable('PERCENT_TEXT', round($this->getVotes(), $this->getRoundPositions()));
			} else {
				$this->tpl->setVariable('PERCENT_TEXT', $this->getVotes());
			}
		}

		$this->tpl->setVariable('ID', $this->getId());
		$this->tpl->setVariable('MAX', $this->getMax());
		$this->tpl->setVariable('TITLE', $this->getTitle());
		if ($this->getOptionLetter()) {
			$this->tpl->setCurrentBlock('option_letter');
			$this->tpl->setVariable('OPTION_LETTER', $this->getOptionLetter());
			$this->tpl->parseCurrentBlock();
		}
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
		$total_votes = $this->getTotal();
		if ($this->getTotal() === 0) {
			return 0;
		}
		$option_votes = $this->getVotes();
		$percentage = ($option_votes / $total_votes) * 100;

		$round = str_replace(',', '0', round($percentage, 1));

		return $round;
	}


	/**
	 * @return float|int
	 */
	protected function getAbsolutePercentage() {
		$total_votes = $this->getMax();
		if ($this->getMax() === 0) {
			return 0;
		}
		$option_votes = $this->getVotes();
		$percentage = ($option_votes / $total_votes) * 100;

		return round($percentage, 1);
	}


	/**
	 * @return int
	 */
	public function getVotes() {
		return $this->votes;
	}


	/**
	 * @param int $votes
	 */
	public function setVotes($votes) {
		$this->votes = $votes;
	}


	/**
	 * @return int
	 */
	public function getTotal() {
		return $this->total;
	}


	/**
	 * @param int $total
	 */
	public function setTotal($total) {
		$this->total = $total;
	}


	/**
	 * @return string
	 */
	public function getOptionLetter() {
		return $this->option_letter;
	}


	/**
	 * @param string $option_letter
	 */
	public function setOptionLetter($option_letter) {
		$this->option_letter = $option_letter;
	}


	/**
	 * @return ilTemplate
	 */
	public function getTpl() {
		return $this->tpl;
	}


	/**
	 * @param ilTemplate $tpl
	 */
	public function setTpl($tpl) {
		$this->tpl = $tpl;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return boolean
	 */
	public function isShowAbsolute() {
		return $this->show_absolute;
	}


	/**
	 * @param boolean $show_absolute
	 */
	public function setShowAbsolute($show_absolute) {
		$this->show_absolute = $show_absolute;
	}


	/**
	 * @return int
	 */
	public function getMax() {
		return $this->max;
	}


	/**
	 * @param int $max
	 */
	public function setMax($max) {
		$this->max = $max;
	}


	/**
	 * @return boolean
	 */
	public function isOverrideBarToPercentage() {
		return $this->override_bar_to_percentage;
	}


	/**
	 * @param boolean $override_bar_to_percentage
	 */
	public function setOverrideBarToPercentage($override_bar_to_percentage) {
		$this->override_bar_to_percentage = $override_bar_to_percentage;
	}


	/**
	 * @return int
	 */
	public function getRoundPositions() {
		return $this->round_positions;
	}


	/**
	 * @param int $round_positions
	 */
	public function setRoundPositions($round_positions) {
		$this->round_positions = $round_positions;
	}


	/**
	 * @return boolean
	 */
	public function isRound() {
		return $this->round;
	}


	/**
	 * @param boolean $round
	 */
	public function setRound($round) {
		$this->round = $round;
	}
}