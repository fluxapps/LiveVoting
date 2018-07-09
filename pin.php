<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * User starts here. Use a RewriteRule to access this page a bit simpler
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once "dir.php";

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Pin\xlvoPin;

try {

	$pin = trim(filter_input(INPUT_GET, "pin"), "/");
	if (!empty($pin)) {

		InitialisationManager::startMinimal();

		if (xlvoPin::checkPin($pin)) {

			//CookieManager::resetCookiePIN();
			CookieManager::resetCookiePUK();
			CookieManager::resetCookieVoting();
			CookieManager::resetCookiePpt();

			CookieManager::setContext(xlvoContext::CONTEXT_PIN);
			CookieManager::setCookiePIN($pin);

			global $DIC;
			$ilCtrl = $DIC->ctrl();
			$ilCtrl->initBaseClass(ilUIPluginRouterGUI::class);
			$ilCtrl->setTargetScript(xlvoConf::getFullApiURL());
			$ilCtrl->redirectByClass([
				ilUIPluginRouterGUI::class,
				xlvoVoter2GUI::class,
			], xlvoVoter2GUI::CMD_START_VOTER_PLAYER);
		}
	}
} catch (Throwable $ex) {

}
