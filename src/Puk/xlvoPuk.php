<?php

namespace LiveVoting\Puk;

use LiveVoting\Pin\xlvoPin;

/**
 * Class xlvoPuk
 *
 * @package LiveVoting\Puk
 */
class xlvoPuk extends xlvoPin {

	/**
	 * @param string $puk
	 */
	public function __construct($puk = "") {
		$this->pin_length = 10;

		parent::__construct($puk);
	}
}
