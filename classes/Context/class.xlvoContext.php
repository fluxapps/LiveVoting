<?php

namespace LiveVoting\Context;

include_once "Services/Context/classes/class.ilContext.php";

/**
 * Class xlvoContext
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoContext extends \ilContext {

	const XLVO_CONTEXT = 'xlvo_context';
	const CONTEXT_PIN = 1;
	const CONTEXT_ILIAS = 2;


	public function __construct() {
		self::init('xlvoContextLiveVoting');
	}


	/**
	 * @param int $context
	 *
	 * @return bool|void
	 */
	public static function init($context) {
		//include_once('class.xlvoContextLiveVoting.php');
		\ilContext::$class_name = 'LiveVoting\Context\xlvoContextLiveVoting';
		\ilContext::$type = - 1;

		return true;
	}
}
