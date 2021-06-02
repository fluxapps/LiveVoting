<?php

namespace LiveVoting\Context\Initialisation\Version\v7;

use ilGlobalTemplate;

/**
 * Class GlobalTemplate
 * @package LiveVoting\Context\Initialisation\Version\v7
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class GlobalTemplate extends ilGlobalTemplate
{
    public function renderPage($part, $a_fill_tabs, $a_skip_main_menu, \ILIAS\DI\Container $DIC) : string
    {
        return parent::renderPage($part, $a_fill_tabs, $a_skip_main_menu, $DIC);
    }

}
