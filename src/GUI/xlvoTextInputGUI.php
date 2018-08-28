<?php

namespace LiveVoting\GUI;

use ilLiveVotingPlugin;
use ilTextInputGUI;
use srag\DIC\DICTrait;

/**
 * Class xlvoTextInputGUI
 *
 * @package LiveVoting\GUI
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoTextInputGUI extends ilTextInputGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * @param string $a_title
	 * @param string $a_postvar
	 */
	public function __construct($a_title = "", $a_postvar = "") {
		parent::__construct($a_title, $a_postvar);
	}
}
