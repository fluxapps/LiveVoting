<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * User starts here. Use a RewriteRule to access this page a bit simpler
 */

require_once __DIR__ . '/../../../../../../../libs/composer/vendor/autoload.php';
require_once __DIR__ . "/vendor/autoload.php";
require_once "dir.php";

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Pin\xlvoPin;
use srag\DIC\LiveVoting\DICStatic;

try {
	$pin = trim(filter_input(INPUT_GET, ParamManager::PARAM_PIN), "/");

	InitialisationManager::startMinimal();

	xlvoContext::setContext(xlvoContext::CONTEXT_PIN);

	//DICStatic::dic()->ctrl()->initBaseClass(ilUIPluginRouterGUI::class);
	DICStatic::dic()->ctrl()->setTargetScript(xlvoConf::getFullApiURL());

	if (!empty($pin)) {

		if (xlvoPin::checkPinAndGetObjId($pin)) {
			$param_manager = ParamManager::getInstance();

			DICStatic::dic()->ctrl()->redirectByClass([
				ilUIPluginRouterGUI::class,
				xlvoVoter2GUI::class,
			], xlvoVoter2GUI::CMD_START_VOTER_PLAYER);
		}
	} else {
		DICStatic::dic()->ctrl()->redirectByClass([
			ilUIPluginRouterGUI::class,
			xlvoVoter2GUI::class,
		], xlvoVoter2GUI::CMD_STANDARD);
	}
} catch (Throwable $ex) {
	echo $ex->getMessage() . "<br /><br /><a href='/'>back</a>";
}
