<?php

namespace srag\CustomInputGUIs\LiveVoting\Waiter;

use srag\DIC\LiveVoting\DICTrait;

/**
 * Class Waiter
 *
 * @package srag\CustomInputGUIs\LiveVoting\Waiter
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
		$dir = __DIR__;
		$dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

		self::dic()->mainTemplate()->addJavaScript($dir . "/js/waiter.min.js");
		self::dic()->mainTemplate()->addCss($dir . "/css/waiter.css");

		self::dic()->mainTemplate()->addOnLoadCode('il.waiter.init("' . $type . '");');
	}


	/**
	 * Waiter constructor
	 */
	private function __construct() {

	}
}
