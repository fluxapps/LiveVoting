<?php
use LiveVoting\Display\Bar\xlvoBarGUI;

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
	protected $max_votes = 100;
	/**
	 * @var string
	 */
	protected $option_letter = '';
	/**
	 * @var \ilTemplate
	 */
	protected $tpl;
	/**
	 * @var string
	 */
	protected $title = '';
	/**
	 * @var bool
	 */
	protected $show_in_percent = false;
	/**
	 * @var int
	 */
	protected $round = 2;


	public function getHTML() {
		$tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_percentage.html', true, true);

		$tpl->setVariable('TITLE', $this->getTitle());

		$tpl->setVariable('ID', uniqid());
		$tpl->setVariable('TITLE', $this->getTitle());

		if ($this->getOptionLetter()) {
			$tpl->setCurrentBlock('option_letter');
			$tpl->setVariable('OPTION_LETTER', $this->getOptionLetter());
			$tpl->parseCurrentBlock();
		}

		if ($this->getMaxVotes() == 0) {
			$calculated_percentage = 0;
		} else {
			$calculated_percentage = $this->getVotes() / $this->getMaxVotes() * 100;
		}

		$tpl->setVariable('MAX', $this->getMaxVotes());
		$tpl->setVariable('PERCENT', $this->getVotes());
		$tpl->setVariable('PERCENT_STYLE', str_replace(',', '.', round($calculated_percentage, 1)));
		if ($this->isShowInPercent()) {
			$tpl->setVariable('PERCENT_TEXT', round($calculated_percentage, $this->getRound())
			                                  . '%');
		} else {
			$tpl->setVariable('PERCENT_TEXT', round($this->getVotes(), $this->getRound()));
		}

		return $tpl->get();
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
	public function getMaxVotes() {
		return $this->max_votes;
	}


	/**
	 * @param int $max_votes
	 */
	public function setMaxVotes($max_votes) {
		$this->max_votes = $max_votes;
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
	 * @return \ilTemplate
	 */
	public function getTpl() {
		return $this->tpl;
	}


	/**
	 * @param \ilTemplate $tpl
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
	 * @return boolean
	 */
	public function isShowInPercent() {
		return $this->show_in_percent;
	}


	/**
	 * @param boolean $show_in_percent
	 */
	public function setShowInPercent($show_in_percent) {
		$this->show_in_percent = $show_in_percent;
	}


	/**
	 * @return int
	 */
	public function getRound() {
		return $this->round;
	}


	/**
	 * @param int $round
	 */
	public function setRound($round) {
		$this->round = $round;
	}
}
