<?php

namespace srag\CustomInputGUIs\Waiter;

use srag\DIC\DICTrait;

/**
 * Class Waiter
 *
 * @package srag\CustomInputGUIs\Waiter
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Waiter {

	use DICTrait;
	/**
	 * @var string
	 */
	const TYPE_WAITER = "waiter";
	/**
	 * @var string
	 */
	const TYPE_PERCENTAGE = "percentage";


	/**
	 * @param string $type
	 */
	public static final function init(/*string*/
		$type)/*: void*/ {
		$dir = substr(__DIR__, strlen(ILIAS_ABSOLUTE_PATH) + 1);

		self::dic()->mainTemplate()->addJavaScript($dir . "/js/waiter.min.js");
		self::dic()->mainTemplate()->addCss($dir . "/css/waiter.min.css");

		self::dic()->mainTemplate()->addOnLoadCode('il.waiter.init("' . $type . '");');
	}


	/**
	 * Waiter constructor
	 */
	private function __construct() {

	}
}
