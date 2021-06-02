<?php

namespace srag\CustomInputGUIs\LiveVoting;

/**
 * Trait CustomInputGUIsTrait
 *
 * @package srag\CustomInputGUIs\LiveVoting
 */
trait CustomInputGUIsTrait
{

    /**
     * @return CustomInputGUIs
     */
    protected static final function customInputGUIs() : CustomInputGUIs
    {
        return CustomInputGUIs::getInstance();
    }
}
