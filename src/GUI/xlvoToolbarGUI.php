<?php

namespace LiveVoting\GUI;

use ilLiveVotingPlugin;
use ilToolbarGUI;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoToolbarGUI
 *
 * @package LiveVoting\GUI
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoToolbarGUI extends ilToolbarGUI {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	protected function applyAutoStickyToSingleElement() {
		return NULL;
	}
}
