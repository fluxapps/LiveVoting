<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * Presenter starts here. Use a RewriteRule to access this page a bit simpler
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once "dir.php";

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\Cookie\CookieManager;
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Voting\xlvoVotingConfig;
use srag\DIC\DICStatic;

try {

	$pin = trim(filter_input(INPUT_GET, "pin"), "/");
	if (!empty($pin)) {

		$puk = trim(filter_input(INPUT_GET, "puk"), "/");
		if (!empty($puk)) {

			InitialisationManager::startMinimal();

			if (xlvoPin::checkPin($pin)) {

				/**
				 * @var xlvoVotingConfig|null $xlvoVotingConfig
				 */
				$xlvoVotingConfig = xlvoVotingConfig::where([ "pin" => $pin ])->first();
				if ($xlvoVotingConfig !== NULL) {

					if ($xlvoVotingConfig->getPuk() === $puk) {

						//CookieManager::resetCookiePIN();
						//CookieManager::resetCookiePUK();
						CookieManager::resetCookieVoting();
						CookieManager::resetCookiePpt();

						CookieManager::setContext(xlvoContext::CONTEXT_PIN);
						CookieManager::setCookiePIN($pin);
						CookieManager::setCookiePUK($puk);

						$voting = trim(filter_input(INPUT_GET, "voting"), "/");
						if (!empty($voting)) {
							CookieManager::setCookieVoting($voting);
						}

						$ppt = trim(filter_input(INPUT_GET, "ppt"), "/");
						if (!empty($ppt)) {
							CookieManager::setCookiePpt($ppt);
						}

						DICStatic::dic()->ctrl()->initBaseClass(ilUIPluginRouterGUI::class);
						DICStatic::dic()->ctrl()->setTargetScript(xlvoConf::getFullApiURL());
						DICStatic::dic()->ctrl()->setParameterByClass(xlvoPlayerGUI::class, "ref_id", current(ilObject2::_getAllReferences($xlvoVotingConfig->getObjId())));
						DICStatic::dic()->ctrl()->redirectByClass([
							ilUIPluginRouterGUI::class,
							xlvoPlayerGUI::class,
						], xlvoPlayerGUI::CMD_START_PRESENTER);
					}
				}
			}
		}
	}
} catch (Throwable $ex) {

}
