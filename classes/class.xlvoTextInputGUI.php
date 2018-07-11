<?php

/**
 * Class xlvoTextInputGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoTextInputGUI extends ilTextInputGUI {

	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;


	/**
	 * @param string $a_title
	 * @param string $a_postvar
	 */
	public function __construct($a_title = "", $a_postvar = "") {
		$this->pl = ilLiveVotingPlugin::getInstance();

		parent::__construct($a_title, $a_postvar);
	}
}
