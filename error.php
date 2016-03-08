<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *
 *         Acts as ILIAS entry-point and calls ILIAS-call-structure
 *         Depending on Context, an ILIAS environment or just the pin context is loaded
 */

require_once('dir.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.xlvoInitialisation.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Js/class.xlvoJs.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConf.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/User/class.xlvoUser.php');
xlvoInitialisation::init();
xlvoUser::getInstance()->setIdentifier(session_id())->setType(xlvoUser::TYPE_PIN);

global $tpl;
ilUtil::sendFailure($_SESSION["failure"]);
ilSession::clear("referer");
ilSession::clear("message");
$tpl->show();