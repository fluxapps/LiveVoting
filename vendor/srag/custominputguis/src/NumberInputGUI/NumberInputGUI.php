<?php

namespace srag\CustomInputGUIs\LiveVoting\NumberInputGUI;

use ilNumberInputGUI;
use ilTableFilterItem;
use ilToolbarItem;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class NumberInputGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\NumberInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NumberInputGUI extends ilNumberInputGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;

    /**
     * @inheritDoc
     */
    public function getTableFilterHTML()/*: string*/
    {
        return $this->render();
    }


    /**
     * @inheritDoc
     */
    public function getToolbarHTML()/*: string*/
    {
        return $this->render();
    }
}
