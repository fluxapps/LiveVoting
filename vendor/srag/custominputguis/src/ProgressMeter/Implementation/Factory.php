<?php

/* Copyright (c) 2017 Ralph Dittrich <dittrich@qualitus.de> Extended GPL, see docs/LICENSE */

namespace srag\CustomInputGUIs\LiveVoting\ProgressMeter\Implementation;

use srag\CustomInputGUIs\LiveVoting\ProgressMeter\Component\Factory as FactoryComponent;

/**
 * Class Factory
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Implementation/Component/Chart/ProgressMeter/Factory.php
 *
 * @package srag\CustomInputGUIs\LiveVoting\ProgressMeter\Implementation
 *
 * @author  Ralph Dittrich <dittrich@qualitus.de>
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since   ILIAS 5.4
 */
class Factory implements FactoryComponent
{

    /**
     * @inheritDoc
     */
    public function standard($maximum, $main, $required = null, $comparison = null)
    {
        return new Standard($maximum, $main, $required, $comparison);
    }


    /**
     * @inheritDoc
     */
    public function fixedSize($maximum, $main, $required = null, $comparison = null)
    {
        return new FixedSize($maximum, $main, $required, $comparison);
    }


    /**
     * @inheritDoc
     */
    public function mini($maximum, $main, $required = null)
    {
        return new Mini($maximum, $main, $required);
    }
}
