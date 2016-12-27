<?php
/**
 * Class: InitialisationManager
 *
 * The InitialisationManager provides different bootstrap behaviors for ilias to enhance performance.
 *
 * User: Nicolas Schaefli <ns@studer-raimann.ch>
 * Date: 10/7/16
 * Time: 1:50 PM
 */

namespace LiveVoting\Context;

require_once("./include/inc.ilias_version.php");

use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\User\xlvoUser;

final class InitialisationManager {

	/**
	 * Starts ILIAS without user and rbag management.
	 * Languages, templates, error handling and database are fully loaded.
	 *
	 * @return void
	 * @throws \Exception   Thrown if no compatible ILIAS version could be found.
	 */
	public static final function startMinimal() {
		CookieManager::setContext(xlvoContext::CONTEXT_PIN);
		$subversion = (int)explode('.', ILIAS_VERSION_NUMERIC)[1];
		switch ($subversion) {
			case ILIASVersionEnum::ILIAS_VERSION_5_0:
				Initialisation\Version\v50\xlvoBasicInitialisation::init();
				break;
			case ILIASVersionEnum::ILIAS_VERSION_5_1:
				Initialisation\Version\v51\xlvoBasicInitialisation::init();
				break;
			case ILIASVersionEnum::ILIAS_VERSION_5_2:
				Initialisation\Version\v52\xlvoBasicInitialisation::init();
				break;
			default:
				throw new \Exception("Can't find bootstrap code for the given ILIAS version.");
		}
		xlvoUser::getInstance()->setIdentifier(session_id())->setType(xlvoUser::TYPE_PIN);
	}


	/**
	 * Optimised ILIAS start with user management.
	 *
	 * @throws \Exception When the user object is invalid.
	 */
	public static final function startLight() {
		xlvoInitialisation::init();

		global $ilUser;

		if ($ilUser instanceof \ilObjUser && $ilUser->getId()) {
			xlvoUser::getInstance()->setIdentifier($ilUser->getId())->setType(xlvoUser::TYPE_ILIAS);

			return;
		}

		throw new \Exception("ILIAS light start failed because the user management returned an invalid user object.");
	}
}