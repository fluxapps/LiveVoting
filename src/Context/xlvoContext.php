<?php

namespace LiveVoting\Context;

use ilContext;
use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoContext
 *
 * @package LiveVoting\Context
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoContext extends ilContext
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const XLVO_CONTEXT = 'xlvo_context';
    const CONTEXT_PIN = 1;
    const CONTEXT_ILIAS = 2;


    public function __construct()
    {
        self::init(xlvoContextLiveVoting::class);
    }


    /**
     * @param int $context
     *
     * @return bool
     */
    public static function init($context)
    {
        ilContext::$class_name = xlvoContextLiveVoting::class;
        ilContext::$type = -1;

        if ($context) {
            self::setContext($context);
        }

        return true;
    }


    /**
     * @return int
     */
    public static function getContext()
    {
        if (!empty($_COOKIE[xlvoContext::XLVO_CONTEXT])) {
            return $_COOKIE[xlvoContext::XLVO_CONTEXT];
        }

        return xlvoContext::CONTEXT_ILIAS;
    }


    /**
     * Sets the xlvo context cookie.
     * This cookie is used to determine the needed bootstrap process.
     * The context constants can be found in the xlvoContext class.
     *
     * @param int $context CONTEXT_ILIAS or CONTEXT_PIN are valid options.
     *
     * @throws Exception Throws exception when the given context is invalid.
     */
    public static function setContext($context)
    {
        if ($context === xlvoContext::CONTEXT_ILIAS || $context === xlvoContext::CONTEXT_PIN) {
            $result = setcookie(xlvoContext::XLVO_CONTEXT, $context, null, '/');
        } else {
            throw new Exception("invalid context received");
        }
        if (!$result) {
            throw new Exception("error setting cookie");
        }
    }
}
