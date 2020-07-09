<?php

/* Copyright (c) 2017 Ralph Dittrich <dittrich@qualitus.de> Extended GPL, see docs/LICENSE */

namespace srag\CustomInputGUIs\LiveVoting\ProgressMeter\Implementation;

use ILIAS\UI\Implementation\Component\ComponentHelper;
use srag\CustomInputGUIs\LiveVoting\ProgressMeter\Component\ProgressMeter as ProgressMeterComponent;

/**
 * Class ProgressMeter
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Implementation/Component/Chart/ProgressMeter/ProgressMeter.php
 *
 * @package srag\CustomInputGUIs\LiveVoting\ProgressMeter\Implementation
 *
 * @author  Ralph Dittrich <dittrich@qualitus.de>
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since   ILIAS 5.4
 */
class ProgressMeter implements ProgressMeterComponent
{

    use ComponentHelper;

    /**
     * @var int
     */
    protected $maximum;
    /**
     * @var int
     */
    private $required;
    /**
     * @var int
     */
    protected $main;
    /**
     * @var int
     */
    protected $comparison;


    /**
     * @inheritDoc
     */
    public function __construct($maximum, $main, $required = null, $comparison = null)
    {
        $this->checkIntArg("maximum", $maximum);
        $this->maximum = $maximum;
        $this->checkIntArg("main", $main);
        $this->main = $this->getSafe($main);

        if ($required != null) {
            $this->checkIntArg("required", $required);
            $this->required = $this->getSafe($required);
        } else {
            $this->checkIntArg("required", $maximum);
            $this->required = $this->getSafe($maximum);
        }
        if ($comparison != null) {
            $this->checkIntArg("comparison", $comparison);
            $this->comparison = $this->getSafe($comparison);
        } else {
            $this->comparison = 0;
        }
    }


    /**
     * @inheritDoc
     */
    public function getMaximum()
    {
        return $this->maximum;
    }


    /**
     * @inheritDoc
     */
    public function getRequired()
    {
        return $this->getSafe($this->required);
    }


    /**
     * Get required value as percent
     *
     * @return int
     */
    public function getRequiredAsPercent()
    {
        return $this->getAsPercentage($this->required);
    }


    /**
     * @inheritDoc
     */
    public function getMainValue()
    {
        return $this->getSafe($this->main);
    }


    /**
     * Get main value as percent
     *
     * @return int
     */
    public function getMainValueAsPercent()
    {
        return $this->getAsPercentage($this->main);
    }


    /**
     * Get integer value "1" if a value is negative or "maximum" if value is more then maximum
     *
     * @param int $a_int
     *
     * @return int
     */
    protected function getSafe($a_int)
    {
        return (($a_int < 0) ? 0 : ($a_int > $this->getMaximum() ? $this->getMaximum() : $a_int));
    }


    /**
     * get an integer value as percent value
     *
     * @param int $a_int
     *
     * @return int
     */
    protected function getAsPercentage($a_int)
    {
        return round(100 / $this->getMaximum() * $this->getSafe($a_int), 0, PHP_ROUND_HALF_UP);
    }
}
