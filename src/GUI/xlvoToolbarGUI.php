<?php

namespace LiveVoting\GUI;

use ilLiveVotingPlugin;
use ilToolbarGUI;
use srag\DIC\DICTrait;

/**
 * Class xlvoToolbarGUI
 *
 * @package LiveVoting\GUI
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoToolbarGUI extends ilToolbarGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	protected function applyAutoStickyToSingleElement() {
		return NULL;
	}
}
