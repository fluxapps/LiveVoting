<?php
require_once('class.xlvoBarGUI.php');

/**
 * Class xlvoDisplayGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoDisplayGUI {

	/**
	 * @var int
	 */
	protected $answer_count = 64;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilObjLiveVoting
	 */
	protected $ilObjLiveVoting;


	/**
	 * @param ilObjLiveVoting $ilObjLiveVoting
	 */
	public function __construct(ilObjLiveVoting $ilObjLiveVoting) {
		global $tpl;
		/**
		 * @var $tpl ilTemplate
		 */
		$tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/bs.css');
		$this->ilObjLiveVoting = $ilObjLiveVoting;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/Display/tpl.display.html', false, true);
	}


	protected function render() {
		$this->tpl->setVariable('TITLE', $this->ilObjLiveVoting->getTitle());
		$this->tpl->setVariable('QUESTION', $this->ilObjLiveVoting->getQuestion());
		$this->tpl->setVariable('PIN', $this->ilObjLiveVoting->getPin());
//		$this->tpl->setVariable('ID', $this->ilObjLiveVoting->getId());
		foreach ($this->ilObjLiveVoting->getOptions() as $option) {
			if ($option instanceof ilLiveVotingOption) {
				$this->addAnswer($option);
				$this->addBar(new xlvoBarGUI($this->ilObjLiveVoting, $option));
			}
		}
	}


	/**
	 * @param ilLiveVotingOption $ilLiveVotingOption
	 */
	protected function addAnswer(ilLiveVotingOption $ilLiveVotingOption) {
		$this->answer_count ++;
		$this->tpl->setCurrentBlock('answer');
		$this->tpl->setVariable('ANSWER_LETTER', (chr($this->answer_count)));
		$this->tpl->setVariable('ANSWER_TEXT', $ilLiveVotingOption->getTitle());
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * @param xlvoBarGUI $xlvoBarGUI
	 */
	public function addBar(xlvoBarGUI $xlvoBarGUI) {
		$this->tpl->setCurrentBlock('bar');
		$this->tpl->setVariable('BAR', $xlvoBarGUI->getHTML());
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * @param $url
	 */
	public function addQRCode($url) {
		$this->tpl->setVariable('QR', '<img src="http://www.reichmann-racing.de/wp-content/download/qrcode.png" width="100%" height="100%">');
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}
}

?>
