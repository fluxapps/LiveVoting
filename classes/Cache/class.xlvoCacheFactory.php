<?php
/**
 * Created by PhpStorm.
 * User: nschaefli
 * Date: 10/12/16
 * Time: 9:15 AM
 */

namespace LiveVoting\Cache;

use LiveVoting\Context\ILIASVersionEnum;

class xlvoCacheFactory {

	/**
	 * Generates an new instance of the live voting service.
	 *
	 * @return xlvoCacheService
	 */
	public static function getInstance() {
		$subversion = (int)explode('.', ILIAS_VERSION_NUMERIC)[1];
		switch ($subversion) {
			case ILIASVersionEnum::ILIAS_VERSION_5_0:
				return Version\v50\xlvoCache::getInstance();
			case ILIASVersionEnum::ILIAS_VERSION_5_1:
				return Version\v51\xlvoCache::getInstance();
			case ILIASVersionEnum::ILIAS_VERSION_5_2:
				return Version\v52\xlvoCache::getInstance('');
		}
	}
}