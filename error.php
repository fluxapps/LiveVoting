<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *
 *         Acts as ILIAS entry-point and calls ILIAS-call-structure
 *         Depending on Context, an ILIAS environment or just the pin context is loaded
 */

use LiveVoting\Context\xlvoInitialisation;
use LiveVoting\User\xlvoUser;

require_once __DIR__ . '/vendor/autoload.php';
require_once('dir.php');
xlvoInitialisation::init();
xlvoUser::getInstance()->setIdentifier(session_id())->setType(xlvoUser::TYPE_PIN);

global $tpl;
ilUtil::sendFailure($_SESSION["failure"]);
ilSession::clear("referer");
ilSession::clear("message");
$tpl->show();