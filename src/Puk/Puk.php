<?php

namespace LiveVoting\Puk;

use LiveVoting\Pin\xlvoPin;

/**
 * Class Puk
 *
 * @package LiveVoting\Puk
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Puk extends xlvoPin
{

    /**
     * @param string $puk
     */
    public function __construct($puk = "")
    {
        $this->pin_length = 10;

        parent::__construct($puk);
    }
}
