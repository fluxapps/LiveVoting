<?php
/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * User starts here. Use a RewriteRule to access this page a bit simpler
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once "dir.php";

use LiveVoting\Api\xlvoApi;
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
