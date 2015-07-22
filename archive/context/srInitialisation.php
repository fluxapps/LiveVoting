<?php
/**
 * Created by JetBrains PhpStorm.
 * @author: Oskar Truffer <ot@studer-raimann.ch>
 * Date: 4/03/13
 * Time: 5:58 PM
 * To change this template use File | Settings | File Templates.
 */
include_once ("Services/Init/classes/class.ilInitialisation.php");

class srInitialisation extends ilInitialisation {
	/**
	 * @var string
	 */
	protected static $context;

	/**
	 * @param ilContext
	 */
	public static function setContext($context) {
		self::$context = $context;
	}
}
