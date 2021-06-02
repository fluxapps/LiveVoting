<?php

namespace srag\DIC\LiveVoting\Cron;

use ilTemplate;

/**
 * Trait FixUITemplateInCronContext
 *
 * @package srag\DIC\LiveVoting\Cron
 */
trait FixUITemplateInCronContext
{

    /**
     *
     */
    protected static function fixUITemplateInCronContext()/*:void*/
    {
        // Fix missing tpl ui in cron context used in some core object constructor
        if (self::dic()->dic()->offsetExists("tpl")) {
            if (!isset($GLOBALS["tpl"])) {
                $GLOBALS["tpl"] = self::dic()->ui()->mainTemplate();
            }
        } else {
            if (!isset($GLOBALS["tpl"])) {
                $GLOBALS["tpl"] = new class() extends ilTemplate {

                    /**
                     * @inheritDoc
                     */
                    public function __construct()
                    {
                        //parent::__construct();
                    }
                };
            }

            self::dic()->dic()->offsetSet("tpl", $GLOBALS["tpl"]);
        }
    }
}
