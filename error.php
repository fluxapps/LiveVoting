<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *
 *         Acts as ILIAS entry-point and calls ILIAS-call-structure
 *         Depending on Context, an ILIAS environment or just the pin context is loaded
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once('dir.php');

use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\Context\xlvoBasicInitialisation;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Context\xlvoInitialisation;
use LiveVoting\User\xlvoUser;

$context = CookieManager::getContext();
switch ($context) {
    case xlvoContext::CONTEXT_PIN:
        xlvoBasicInitialisation::init();
        xlvoUser::getInstance()->setIdentifier(session_id())->setType(xlvoUser::TYPE_PIN);
        break;

    case xlvoContext::CONTEXT_ILIAS:
        xlvoInitialisation::init();
        global $ilUser;
        xlvoUser::getInstance()->setIdentifier($ilUser->getId())->setType(xlvoUser::TYPE_ILIAS);
        break;
}

global $tpl;
ilUtil::sendFailure($_SESSION["failure"]);
ilSession::clear("referer");
ilSession::clear("message");
$tpl->show();