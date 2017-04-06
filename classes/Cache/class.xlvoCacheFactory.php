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

    private static $cache_instance = null;

	/**
	 * Generates an new instance of the live voting service.
	 *
	 * @return xlvoCacheService
	 */
	public static function getInstance() {

	    if(self::$cache_instance === null)
        {
            $subversion = (int)explode('.', ILIAS_VERSION_NUMERIC)[1];
            switch ($subversion) {
                case ILIASVersionEnum::ILIAS_VERSION_5_1:
                    self::$cache_instance = Version\v51\xlvoCache::getInstance();
                    break;
                case ILIASVersionEnum::ILIAS_VERSION_5_2:
                    self::$cache_instance = Version\v52\xlvoCache::getInstance('');
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