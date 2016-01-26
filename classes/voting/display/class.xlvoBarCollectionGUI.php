<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/display/class.xlvoBarGUI.php');

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


	public function __construct() {
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/tpl.bar_collection.html', true, true);
	}


	/**
	 * @return string
	 */
	public function getHTML() {

		return $this->tpl->get();
	}


	/**
	 * @param $bar_gui xlvoBarGUI
	 */
	public function addBar(xlvoBarGUI $bar_gui) {
		if ($bar_gui instanceof xlvoBarGUI) {
			$this->tpl->setCurrentBlock('bar');
			$this->tpl->setVariable('BAR', $bar_gui->getHTML());
			$this->tpl->parseCurrentBlock();
		}
	}
}