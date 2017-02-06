<?php
use LiveVoting\Display\Bar\xlvoBarGUI;

/**
 * Class xlvoBarCollectionGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoBarCollectionGUI {

	/**
	 * @var \ilTemplate
	 */
	protected $tpl;
	/**
	 * @var int
	 */
	protected $total_votes = 0;
	/**
	 * @var bool
	 */
	protected $show_total_votes = false;
	/**
	 * @var int
	 */
	protected $total_voters = 0;
	/**
	 * @var bool
	 */
	protected $show_total_voters = false;


	public function __construct() {
		$this->tpl = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_collection.html', true, true);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		if ($this->isShowTotalVotes()) {
			$this->tpl->setCurrentBlock('total_votes');
			$this->tpl->setVariable('TOTAL_VOTES', ilLiveVotingPlugin::getInstance()->txt('qtype_1_total_votes') . ': ' . $this->getTotalVotes());
			$this->tpl->parseCurrentBlock();
		}
		if ($this->isShowTotalVoters()) {
			$this->tpl->setCurrentBlock('total_voters');
			$this->tpl->setVariable('TOTAL_VOTERS', ilLiveVotingPlugin::getInstance()->txt('qtype_1_total_voters') . ': ' . $this->getTotalVoters());
			$this->tpl->parseCurrentBlock();
		}

		return $this->tpl->get();
	}


	/**
	 * @param $bar_gui xlvoBarGUI
	 */
	public function addBar(xlvoBarGUI $bar_gui) {
		$this->tpl->setCurrentBlock('bar');
		$this->tpl->setVariable('BAR', $bar_gui->getHTML());
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * @param $html
	 */
	public function addSolution($html) {
		$this->tpl->setCurrentBlock('solution');
		$this->tpl->setVariable('SOLUTION', $html);
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * @return int
	 */
	public function getTotalVotes() {
		return $this->total_votes;
	}


	/**
	 * @param int $total_votes
	 */
	public function setTotalVotes($total_votes) {
		$this->total_votes = $total_votes;
	}


	/**
	 * @return boolean
	 */
	public function isShowTotalVotes() {
		return $this->show_total_votes;
	}


	/**
	 * @param boolean $show_total_votes
	 */
	public function setShowTotalVotes($show_total_votes) {
		$this->show_total_votes = $show_total_votes;
	}


	/**
	 * @return int
	 */
	public function getTotalVoters() {
		return $this->total_voters;
	}


	/**
	 * @param int $total_voters
	 */
	public function setTotalVoters($total_voters) {
		$this->total_voters = $total_voters;
	}


	/**
	 * @return boolean
	 */
	public function isShowTotalVoters() {
		return $this->show_total_voters;
	}


	/**
	 * @param boolean $show_total_voters
	 */
	public function setShowTotalVoters($show_total_voters) {
		$this->show_total_voters = $show_total_voters;
	}
}