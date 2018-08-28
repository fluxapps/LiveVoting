<?php

namespace LiveVoting\Context;

use ilContext;
use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class xlvoContext
 *
 * @package LiveVoting\Context
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoContext extends ilContext {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
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
		ilContext::$class_name = xlvoContextLiveVoting::class;
		ilContext::$type = - 1;

		return true;
	}
}
