<?php

namespace srag\DIC\DIC;

use srag\DIC\DICTrait;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\DIC
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractDIC implements DICInterface {

	use DICTrait;


	/**
	 * AbstractDIC constructor
	 */
	protected function __construct() {

	}
}
