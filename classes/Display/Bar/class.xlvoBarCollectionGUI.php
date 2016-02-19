<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarGUI.php');

/**
 * Class xlvoBarCollectionGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoBarCollectionGUI {

	/**
	 * @var ilTemplate
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


	public function __construct() {
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_collection.html', true, true);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		if ($this->isShowTotalVotes()) {
			$this->tpl->setCurrentBlock('total');
			$this->tpl->setVariable('TOTAL', ilLiveVotingPlugin::getInstance()->txt('qtype_1_total_votes') . ': ' . $this->getTotalVotes());
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
}