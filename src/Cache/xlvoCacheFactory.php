<?php

namespace LiveVoting\Cache;

use ilException;
use ilLiveVotingPlugin;
use LiveVoting\Cache\Version\v52\xlvoCache;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoCacheFactory
 *
 * @package LiveVoting\Cache
 * @author  nschaefli
 */
class xlvoCacheFactory
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    private static $cache_instance = null;


    /**
     * Generates an new instance of the live voting service.
     *
     * @return xlvoCacheService
     */
    public static function getInstance()
    {
        if (self::$cache_instance === null) {
            self::$cache_instance = xlvoCache::getInstance('');

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
