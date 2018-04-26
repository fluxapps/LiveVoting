<?php

namespace LiveVoting\Cache;

use LiveVoting\Context\ILIASVersionEnum;

/**
 * Class xlvoCacheFactory
 *
 * @author  nschaefli
 * @package LiveVoting\Cache
 */
class xlvoCacheFactory {

	private static $cache_instance = NULL;


	/**
	 * Generates an new instance of the live voting service.
	 *
	 * @return xlvoCacheService
	 */
	public static function getInstance() {

		if (self::$cache_instance === NULL) {
			$subversion = (int)explode('.', ILIAS_VERSION_NUMERIC)[1];
			switch ($subversion) {
				case ILIASVersionEnum::ILIAS_VERSION_5_2:
				case ILIASVersionEnum::ILIAS_VERSION_5_3:
					self::$cache_instance = Version\v52\xlvoCache::getInstance('');
					break;
				default:
					throw new ilException('Can not initialise cache for the installed ILIAS version.');
					break;
			}

			/*
			 * caching adapter of the xlvoConf will call getInstance again,
			 * due to that we need to call the init logic after we created the
			 * cache in an deactivated state.
			 *
			 * The xlvoConf call gets the deactivated cache and query the value
			 * out of the database. afterwards the cache is turned on with this init() call.
			 *
			 * This must be considered as workaround and should be probably fixed in the next major release.
			 */
			self::$cache_instance->init();
		}

		return self::$cache_instance;
	}
}