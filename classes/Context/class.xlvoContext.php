<?php

namespace LiveVoting\Context;

use ilContext;

/**
 * Class xlvoContext
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoContext extends ilContext {

	const XLVO_CONTEXT = 'xlvo_context';
	const CONTEXT_PIN = 1;
	const CONTEXT_ILIAS = 2;


	public function __construct() {
		self::init(xlvoContextLiveVoting::class);
	}


	/**
	 * @param int $context
	 *
	 * @return bool
	 */
	public static function init($context) {
		ilContext::$class_name = 'LiveVoting\Context\xlvoContextLiveVoting';
		ilContext::$type = - 1;

		return true;
	}
}
