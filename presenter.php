<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * Presenter starts here. Use a RewriteRule to access this page a bit simpler
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once "dir.php";

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Voting\xlvoVotingConfig;
use srag\DIC\LiveVoting\DICStatic;

try {

	$pin = trim(filter_input(INPUT_GET, ParamManager::PARAM_PIN), "/");
	$puk = trim(filter_input(INPUT_GET, ParamManager::PARAM_PUK), "/");

	if (!empty($pin)) {
		InitialisationManager::startMinimal();

		if (xlvoPin::checkPinAndGetObjId($pin) && !empty($puk)) {

			$param_manager = ParamManager::getInstance();

			/**
			 * @var xlvoVotingConfig|null $xlvoVotingConfig
			 */
			$xlvoVotingConfig = xlvoVotingConfig::where([ "pin" => $pin ])->first();
			if ($xlvoVotingConfig !== NULL) {

				if ($xlvoVotingConfig->getPuk() === $puk) {

					xlvoContext::setContext(xlvoContext::CONTEXT_PIN);

					DICStatic::dic()->ctrl()->setTargetScript(xlvoConf::getFullApiURL());
					DICStatic::dic()->ctrl()->redirectByClass([
						ilUIPluginRouterGUI::class,
						xlvoPlayerGUI::class,
					], xlvoPlayerGUI::CMD_START_PRESENTER);
				}
			}
		}
	}
} catch (Throwable $ex) {
	echo $ex->getMessage() . "<br /><br /><a href='/'>back</a>";
}
