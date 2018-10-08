<?php

namespace LiveVoting\Context;

require_once 'include/inc.ilias_version.php';

use Exception;
use ilLiveVotingPlugin;
use ilObjUser;
use LiveVoting\Context\Cookie\CookieManager;
use LiveVoting\User\xlvoUser;
use srag\DIC\DICTrait;

/**
 * Class: InitialisationManager
 *
 * @package LiveVoting\Context
 *
 * The InitialisationManager provides different bootstrap behaviors for ilias to enhance performance.
 *
 * User: Nicolas Schaefli <ns@studer-raimann.ch>
 * Date: 10/7/16
 * Time: 1:50 PM
 */
final class InitialisationManager {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * Starts ILIAS without user and rbag management.
	 * Languages, templates, error handling and database are fully loaded.
	 *
	 * @return void
	 * @throws Exception   Thrown if no compatible ILIAS version could be found.
	 */
	public static final function startMinimal() {
		CookieManager::setContext(xlvoContext::CONTEXT_PIN);
		$subversion = (int)explode('.', self::version()->getILIASVersion())[1];
		switch ($subversion) {
			case ILIASVersionEnum::ILIAS_VERSION_5_2:
				Initialisation\Version\v52\xlvoBasicInitialisation::init();
				break;
			case ILIASVersionEnum::ILIAS_VERSION_5_3:
				Initialisation\Version\v53\xlvoBasicInitialisation::init();
				break;
			default:
				throw new Exception("Can't find bootstrap code for the given ILIAS version.");
		}
		xlvoUser::getInstance()->setIdentifier(session_id())->setType(xlvoUser::TYPE_PIN);
	}


	/**
	 * Optimised ILIAS start with user management.
	 *
	 * @throws Exception When the user object is invalid.
	 */
	public static final function startLight() {
		xlvoInitialisation::init();

		if (self::dic()->user() instanceof ilObjUser && self::dic()->user()->getId()) {
			xlvoUser::getInstance()->setIdentifier(self::dic()->user()->getId())->setType(xlvoUser::TYPE_ILIAS);

			return;
		}

		throw new Exception("ILIAS light start failed because the user management returned an invalid user object.");
	}


	private function __construct() {

	}
}
