<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * Acts as ILIAS entry-point and calls ILIAS-call-structure
 * Depending on Context, an ILIAS environment or just the pin context is loaded
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once "dir.php";

use LiveVoting\Context\Cookie\CookieManager;
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\xlvoContext;
use srag\DIC\DICStatic;

$context = CookieManager::getContext();
switch ($context) {
	case xlvoContext::CONTEXT_PIN:
		InitialisationManager::startMinimal();
		break;

	case xlvoContext::CONTEXT_ILIAS:
	default:
		InitialisationManager::startLight();
		break;
}

ilUtil::sendFailure($_SESSION["failure"]);
ilSession::clear("referer");
ilSession::clear("message");
DICStatic::dic()->template()->show();
