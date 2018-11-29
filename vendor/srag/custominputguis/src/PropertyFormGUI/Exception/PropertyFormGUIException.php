<?php

namespace srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Exception;

use ilFormException;

/**
 * Class PropertyFormGUIException
 *
 * @package srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class PropertyFormGUIException extends ilFormException {

	/**
	 * PropertyFormGUIException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @internal
	 */
	public function __construct(/*string*/
		$message, /*int*/
		$code = 0) {
		parent::__construct($message, $code);
	}
}
