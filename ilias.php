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
use LiveVoting\Context\xlvoInitialisation;
use LiveVoting\User\xlvoUser;

xlvoInitialisation::init();
global $ilUser;
if ($ilUser instanceof ilObjUser && $ilUser->getId()) {
	xlvoUser::getInstance()->setIdentifier($ilUser->getId())->setType(xlvoUser::TYPE_ILIAS);
} else {
	xlvoUser::getInstance()->setIdentifier(session_id())->setType(xlvoUser::TYPE_PIN);
}
global $ilCtrl, $ilBench;
/**
 * @var ilCtrl $ilCtrl
 */
xlvoConf::load();

$ilCtrl->setTargetScript(xlvoConf::getFullApiURL());
$ilCtrl->callBaseClass();
$ilBench->save();