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

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\xlvoBasicInitialisation;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Context\xlvoInitialisation;
use LiveVoting\User\xlvoUser;

$context = CookieManager::getContext();
switch ($context) {
    case xlvoContext::CONTEXT_PIN:
        InitialisationManager::startMinimal();
        break;

    case xlvoContext::CONTEXT_ILIAS:
        InitialisationManager::startLight();
        //TODO: catch error if user used the go to link but has no ilias authentication. Atm the error handling page is shown.
        break;
}

xlvoConf::load();

global $DIC;
$ilCtrl = $DIC->ctrl();
$ilBench = $DIC["ilBench"];
$ilCtrl->setTargetScript(xlvoConf::getFullApiURL());
$ilCtrl->callBaseClass();
$ilBench->save();
