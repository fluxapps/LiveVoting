<?php

/* Copyright (c) 2017 Ralph Dittrich <dittrich@qualitus.de> Extended GPL, see docs/LICENSE */

namespace srag\CustomInputGUIs\LiveVoting\ProgressMeter\Implementation;

use srag\CustomInputGUIs\LiveVoting\ProgressMeter\Component\Standard as StandardComponent;

/**
 * Class Standard
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Implementation/Component/Chart/ProgressMeter/Standard.php
 *
 * @package srag\CustomInputGUIs\LiveVoting\ProgressMeter\Implementation
 *
 * @author  Ralph Dittrich <dittrich@qualitus.de>
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since   ILIAS 5.4
 */
class Standard extends ProgressMeter implements StandardComponent
{

    /**
     * @var string
     */
    protected $main_text;
    /**
     * @var string
     */
    protected $required_text;


    /**
     * @inheritDoc
     */
    public function getComparison()
    {
        return $this->getSafe($this->comparison);
    }


    /**
     * Get comparison value as percent
     *
     * @return int
     */
    public function getComparisonAsPercent()
    {
        return $this->getAsPercentage($this->comparison);
    }


    /**
     * @inheritDoc
     */
    public function withMainText($text)
    {
        $this->checkStringArg("main_value_text", $text);

        $clone = clone $this;
        $clone->main_text = $text;

        return $clone;
    }


    /**
     * @inheritDoc
     */
    public function getMainText()
    {
        return $this->main_text;
    }


    /**
     * @inheritDoc
     */
    public function withRequiredText($text)
    {
        $this->checkStringArg("required_value_text", $text);

        $clone = clone $this;
        $clone->required_text = $text;

        return $clone;
    }


    /**
     * @inheritDoc
     */
    public function getRequiredText()
    {
        return $this->required_text;
    }
}
