<?php

/**
 * Class xlvoBarGUI
 *
 * @author             Fabian Schmid <fs@studer-raimann.ch>
 * @version            1.0.0
 * @ilCtrl_IsCalledBy  xlvoBarGUI: ilObjLiveVotingGUI
 */
class xlvoBarGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilLiveVotingOption
	 */
	protected $ilLiveVotingOption;
	/**
	 * @var ilObjLiveVoting
	 */
	protected $ilObjLiveVoting;


	/**
	 * @param ilObjLiveVoting    $ilObjLiveVoting
	 * @param ilLiveVotingOption $ilLiveVotingOption
	 */
	public function __construct(ilObjLiveVoting $ilObjLiveVoting, ilLiveVotingOption $ilLiveVotingOption) {
		global $tpl;
		/**
		 * @var $tpl ilTemplate
		 */
		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/Display/bar.js');

		$this->ilObjLiveVoting = $ilObjLiveVoting;
		$this->ilLiveVotingOption = $ilLiveVotingOption;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/Display/tpl.bar.html', false, false);
	}


	protected function render() {
		$this->tpl->setVariable('PERCENT', $this->ilObjLiveVoting->getPercentageForOption($this->ilLiveVotingOption->getId()));
		$this->tpl->setVariable('ID', $this->ilLiveVotingOption->getId());
		$this->tpl->setVariable('CIPHER', $this->ilLiveVotingOption->countVotes());
		$this->tpl->setVariable('COUNT', $this->ilLiveVotingOption->countVotes());
		$this->tpl->setVariable('AJAX_LINK', $this->ilLiveVotingOption->getId());
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}


	/**
	 * @param ilObjLiveVoting $ilObjLiveVoting
	 *
	 * @return string
	 */
	public function getAjaxData(ilObjLiveVoting $ilObjLiveVoting) {
		$data = array(
			'percentage' => $ilObjLiveVoting->getPercentageForOption($this->ilLiveVotingOption->getId()),
			'percentage_relative' => $ilObjLiveVoting->getRelativePercentageForOption($this->ilLiveVotingOption->getId()),
			'votes' => $this->ilLiveVotingOption->countVotes(),
		);

		return json_encode($data);
	}
}

?>
