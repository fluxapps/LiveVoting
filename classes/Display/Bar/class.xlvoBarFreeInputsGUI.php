<?php

use LiveVoting\Display\Bar\xlvoBarGUI;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;

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
	 * @var \ilTemplate
	 */
	protected $tpl;
	/**
	 * @var int $occurrences
	 */
	private $occurrences;
	/**
	 * @var bool
	 */
	private $strong = false;
	/**
	 * @var bool
	 */
	private $center = false;
	/**
	 * @var bool
	 */
	private $big = false;


	/**
	 * @param xlvoVoting $voting
	 * @param xlvoVote   $vote
	 */
	public function __construct(xlvoVoting $voting, xlvoVote $vote) {
		$this->voting = $voting;
		$this->vote = $vote;
		$this->tpl = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_free_input.html', true, true);
		$this->occurrences = 0;
	}


	protected function render() {
		$this->tpl->setVariable('FREE_INPUT', $this->vote->getFreeInput());

		if ($this->isCenter()) {
			$this->tpl->touchBlock('center');
		}
		if ($this->isBig()) {
			$this->tpl->touchBlock('big');
		}
		if ($this->isStrong()) {
			$this->tpl->touchBlock('strong');
			$this->tpl->touchBlock('strong_end');
		}

		if ($this->occurrences > 1) {
			$this->tpl->setVariable('GROUPED_BARS_COUNT', $this->occurrences);
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
	 * @return int
	 */
	public function getOccurrences() {
		return $this->occurrences;
	}


	/**
	 * Compares the freetext of the current with the given object.
	 * This function returns true if the free text is case insensitive equal to the
	 * given one.
	 *
	 * @param xlvoBarFreeInputsGUI $bar The object which should be used for the comparison.
	 *
	 * @return bool True if the freetext is case insensitive equal to the given one.
	 */
	public function equals(xlvoBarFreeInputsGUI $bar) {
		return strcasecmp($this->vote->getFreeInput(), $bar->vote->getFreeInput()) === 0;
	}


	/**
	 * @param int $occurrences
	 */
	public function setOccurrences($occurrences) {
		$this->occurrences = $occurrences;
	}


	/**
	 * @return bool
	 */
	public function isStrong() {
		return $this->strong;
	}


	/**
	 * @param bool $strong
	 */
	public function setStrong($strong) {
		$this->strong = $strong;
	}


	/**
	 * @return bool
	 */
	public function isCenter() {
		return $this->center;
	}


	/**
	 * @param bool $center
	 */
	public function setCenter($center) {
		$this->center = $center;
	}


	/**
	 * @return bool
	 */
	public function isBig() {
		return $this->big;
	}


	/**
	 * @param bool $big
	 */
	public function setBig($big) {
		$this->big = $big;
	}
}
