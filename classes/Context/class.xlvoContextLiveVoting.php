<?php

namespace LiveVoting\Context;

// TODO SRAG-GC IMPLEMENT INTERFACE - EXPLINATION: In ILIAS 5.2 the abstract class ilContextBase has been replaced by the interface ilContextTemplate.
// TODO SRAG-GC IMPLEMENT INTERFACE - EXPLINATION: In order to stay compatible as well with ILIAS < 5.2 and ILIAS >= 5.2, we removed as well "extends ilContextBase" as "implement ilContextTemplate"
// TODO SRAG-GC IMPLEMENT INTERFACE - EXPLINATION: However, as soon as this plugin won't support ILIAS 5.1 anymore, we should implement the interface again

// TODO SRAG-GC IMPLEMENT INTERFACE - STEP 1: as soon as this plugin does not support ILIAS 5.1 anymore, uncomment the following line
//require_once('Services/Context/interfaces/interface.ilContextTemplate.php');

/**
 * Class xlvoContextLiveVoting
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 */
// TODO SRAG-GC IMPLEMENT INTERFACE - STEP 2: as soon as this plugin does not support ILIAS 5.1 anymore, uncomment the following line, and remove the after next line
//class xlvoContextLiveVoting implements ilContextTemplate {
class xlvoContextLiveVoting {

	/**
	 * @return bool
	 */
	public static function supportsRedirects() {
		return false;
	}


	/**
	 * @return bool
	 */
	public static function hasUser() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function usesHTTP() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function hasHTML() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function usesTemplate() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function initClient() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function doAuthentication() {
		return false;
	}
}
