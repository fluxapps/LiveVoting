<?php

namespace LiveVoting\Puk;

use LiveVoting\Pin\xlvoPin;

/**
 *
 */
class xlvoPuk extends xlvoPin {

	/**
	 * @param string $pin
	 */
	public function __construct(string $pin = "") {
		$this->pin_length = 10;

		parent::__construct($pin);
	}
}
