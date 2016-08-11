<?php

/**
 * Class xlvoObjectDefinition
 *
 * This mocks ilObjectDefinition in PIN Context (bc of ilObjMediaObject in Text)
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoObjectDefinition {

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
}
