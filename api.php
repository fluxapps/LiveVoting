<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * User starts here. Use a RewriteRule to access this page a bit simpler
 */

require_once __DIR__ . '/../../../../../../../libs/composer/vendor/autoload.php';
require_once __DIR__ . "/vendor/autoload.php";
require_once "dir.php";

use LiveVoting\Api\xlvoApi;
use LiveVoting\Context\InitialisationManager;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Pin\xlvoPin;

try {

	$pin = trim(filter_input(INPUT_GET, "pin"), "/");
	$puk = trim(filter_input(INPUT_GET, "puk"), "/");

	if (!empty($pin)) {

		InitialisationManager::startMinimal();

		if (xlvoPin::checkPinAndGetObjId($pin)) {

			$param_manager = ParamManager::getInstance();
			xlvoContext::setContext(xlvoContext::CONTEXT_PIN);

			$token = trim(filter_input(INPUT_GET, "token"), "/");
			if (!empty($token)) {

				$api = new xlvoApi(new xlvoPin($pin), $token);

				$type = trim(filter_input(INPUT_GET, "type"), "/");
				if (!empty($type)) {
					$api->setType($type);
				}

				$api->send();
			}
		}
	}
} catch (Throwable $ex) {

}
