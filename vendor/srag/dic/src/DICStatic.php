<?php

namespace srag\DIC;

/**
 * Class DICStatic
 *
 * @package srag\DIC
 */
final class DICStatic {

	/**
	 * Get DIC interface
	 *
	 * @return DICInterface DIC interface
	 * @ throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	public static function dic() {
		return DICCache::dic();
	}


	/**
	 * DICStatic constructor
	 */
	private function __construct() {

	}
}
