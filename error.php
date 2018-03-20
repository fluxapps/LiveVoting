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
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\xlvoContext;

$context = CookieManager::getContext();
switch ($context) {
    case xlvoContext::CONTEXT_PIN:
        InitialisationManager::startMinimal();
        break;

    case xlvoContext::CONTEXT_ILIAS:
        InitialisationManager::startLight();
        break;
}

global $DIC;
ilUtil::sendFailure($_SESSION["failure"]);
ilSession::clear("referer");
ilSession::clear("message");
$DIC->ui()->mainTemplate()->show();