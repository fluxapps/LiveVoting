<?php

namespace LiveVoting\Context;

use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoObjectDefinition
 *
 * This mocks ilObjectDefinition in PIN Context (bc of ilObjMediaObject in Text)
 *
 * @package LiveVoting\Context
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoObjectDefinition {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * @return bool
	 */
	public function isRBACObject() {
		return false;
	}


	/**
	 * @return string
	 */
	public function getTranslationType() {
		return ''; //"sys"
	}


	public function getOrgUnitPermissionTypes() {
		return [];
	}
}
