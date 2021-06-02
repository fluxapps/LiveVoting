<?php

namespace LiveVoting\Context;

require_once 'include/inc.ilias_version.php';

use Exception;
use ilLiveVotingPlugin;
use ilObjUser;
use LiveVoting\User\xlvoUser;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class: InitialisationManager
 *
 * @package LiveVoting\Context
 *
 * The InitialisationManager provides different bootstrap behaviors for ilias to enhance performance.
 *
 * User: Nicolas Schaefli <ns@studer-raimann.ch>
 * Date: 10/7/16
 * Time: 1:50 PM
 */
final class InitialisationManager
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


    /**
     * Starts ILIAS without user and rbag management.
     * Languages, templates, error handling and database are fully loaded.
     *
     * @return void
     * @throws Exception   Thrown if no compatible ILIAS version could be found.
     */
    public static final function startMinimal()
    {
        switch (true) {
            case self::version()->is7():
                Initialisation\Version\v7\xlvoBasicInitialisation::init();
                break;
            case self::version()->is6():
                Initialisation\Version\v6\xlvoBasicInitialisation::init();
                break;
            default:
                throw new Exception("Can't find bootstrap code for the given ILIAS version.");
        }

        xlvoUser::getInstance()->setIdentifier(session_id())->setType(xlvoUser::TYPE_PIN);
    }


    /**
     * Optimised ILIAS start with user management.
     *
     * @throws Exception When the user object is invalid.
     */
    public static final function startLight()
    {
        xlvoInitialisation::init();

        if (!(self::dic()->user() instanceof xlvoDummyUser) && self::dic()->user()->getId()) {
            xlvoUser::getInstance()->setIdentifier(self::dic()->user()->getId())->setType(xlvoUser::TYPE_ILIAS);

            return;
        }

        throw new Exception("ILIAS light start failed because the user management returned an invalid user object.");
    }


    private function __construct()
    {

    }
}
