<?php
/**
 * Class: InitialisationManager
 *
 * The InitialisationManager provides different bootstrap behaviors for ilias to enhance performance.
 *
 * User: Nicolas Schaefli <ns@studer-raimann.ch>
 * Date: 10/7/16
 * Time: 1:50 PM
 */

namespace LiveVoting\Context;


use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\User\xlvoUser;

final class InitialisationManager
{
    /**
     * Starts ILIAS without user and rbag management.
     * Languages, templates, error handling and database are fully loaded.
     *
     * @return void
     */
    public static final function startMinimal()
    {
        CookieManager::setContext(xlvoContext::CONTEXT_PIN);
        xlvoBasicInitialisation::init();
        xlvoUser::getInstance()->setIdentifier(session_id())->setType(xlvoUser::TYPE_PIN);
    }

    /**
     * Optimised ILIAS start with user management.
     * @throws \Exception When the user object is invalid.
     */
    public static final function startLight()
    {
        xlvoInitialisation::init();

        global $ilUser;

        if($ilUser instanceof \ilObjUser && $ilUser->getId())
        {
            xlvoUser::getInstance()->setIdentifier($ilUser->getId())->setType(xlvoUser::TYPE_ILIAS);
            return;
        }

        throw new \Exception("ILIAS light start failed because the user management returned an invalid user object.");
    }

}