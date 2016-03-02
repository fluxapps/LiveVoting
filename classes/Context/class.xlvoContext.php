<?php
include_once "Services/Context/classes/class.ilContext.php";

/**
 * Class xlvoContext
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoContext extends ilContext {

	public function __construct() {
		self::init('xlvoContextLiveVoting');
	}


	/**
	 * @param int $context
	 *
	 * @return bool|void
	 */
	public static function init($context) {
		include_once('class.xlvoContextLiveVoting.php');
		ilContext::$class_name = 'xlvoContextLiveVoting';
		ilContext::$type = - 1;

		return true;
	}
}
