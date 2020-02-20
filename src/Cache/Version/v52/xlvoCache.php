<?php

namespace LiveVoting\Cache\Version\v52;

use Exception;
use ilApc;
use ilGlobalCache;
use ilGlobalCacheService;
use ilLiveVotingPlugin;
use ilMemcache;
use ilStaticCache;
use ilXcache;
use LiveVoting\Cache\Initialisable;
use LiveVoting\Cache\xlvoCacheService;
use LiveVoting\Conf\xlvoConf;
use RuntimeException;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xoctCache
 *
 * @package LiveVoting\Cache\Version\v52
 * @author  nschaefli
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoCache extends ilGlobalCache implements xlvoCacheService, Initialisable
{

    use DICTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var bool
     */
    protected static $override_active = false;
    /**
     * @var array
     */
    protected static $active_components
        = array(
            ilLiveVotingPlugin::PLUGIN_ID,
        );


    /**
     * @param null $component
     *
     * @return xlvoCache
     */
    public static function getInstance($component)
    {
        $service_type = self::getSettings()->getService();
        $xlvoCache = new self($service_type);

        //must be disabled because the xlvoConf loads the data via xlvoCache which is not fully initialised at this point.
        $xlvoCache->setActive(false);
        self::setOverrideActive(false);

        return $xlvoCache;
    }


    /**
     * Init the cache.
     */
    public function init()
    {
        $this->initCachingService();
        $this->setActive(true);
        self::setOverrideActive(true);
    }


    protected function initCachingService()
    {
        /**
         * @var ilGlobalCacheService $ilGlobalCacheService
         */
        if (!$this->getComponent()) {
            $this->setComponent(ilLiveVotingPlugin::PLUGIN_NAME);
        }

        $ilGlobalCacheService = null;

        if ($this->isLiveVotingCacheEnabled()) {
            $serviceName = self::lookupServiceClassName($this->getServiceType());
            $ilGlobalCacheService = new $serviceName(self::$unique_service_id, $this->getComponent());
            $ilGlobalCacheService->setServiceType($this->getServiceType());
        } else {
            $serviceName = self::lookupServiceClassName(self::TYPE_STATIC);
            $ilGlobalCacheService = new $serviceName(self::$unique_service_id, $this->getComponent());
            $ilGlobalCacheService->setServiceType(self::TYPE_STATIC);
        }

        $this->global_cache = $ilGlobalCacheService;
        $this->setActive(in_array($this->getComponent(), self::getActiveComponents()));
    }


    /**
     * Checks if live voting is able to use the global cache.
     *
     * @return bool
     */
    private function isLiveVotingCacheEnabled()
    {
        try {
            return (int) xlvoConf::getConfig(xlvoConf::F_USE_GLOBAL_CACHE) === 1;
        } catch (Exception $exceptione) //catch exception while dbupdate is running. (xlvoConf is not ready at that time).
        {
            return false;
        }
    }


    /**
     * @param $service_type
     *
     * @return string
     */
    public static function lookupServiceClassName($service_type)
    {
        switch ($service_type) {
            case self::TYPE_APC:
                return ilApc::class;
                break;
            case self::TYPE_MEMCACHED:
                return ilMemcache::class;
                break;
            case self::TYPE_XCACHE:
                return ilXcache::class;
                break;
            case self::TYPE_STATIC:
                return ilStaticCache::class;
                break;
            default:
                return ilStaticCache::class;
                break;
        }
    }


    /**
     * @return array
     */
    public static function getActiveComponents()
    {
        return self::$active_components;
    }


    /**
     * @param bool $complete
     *
     * @return bool
     * @throws RuntimeException
     */
    public function flush($complete = false)
    {
        if (!$this->global_cache instanceof ilGlobalCacheService || !$this->isActive()) {
            return false;
        }

        return parent::flush($complete);
    }


    /**
     * Manually removes a cached value.
     *
     * @param string $key The unique key which represents the value.
     *
     * @return bool
     * @throws RuntimeException
     */
    public function delete($key)
    {
        if (!$this->global_cache instanceof ilGlobalCacheService || !$this->isActive()) {
            return false;
        }

        return parent::delete($key);
    }


    /**
     * @return bool
     */
    public function isActive()
    {
        return self::isOverrideActive();
    }


    /**
     * @return boolean
     */
    public static function isOverrideActive()
    {
        return self::$override_active;
    }


    /**
     * @param boolean $override_active
     */
    public static function setOverrideActive($override_active)
    {
        self::$override_active = $override_active;
    }


    /**
     * @param string $key   An unique key.
     * @param mixed  $value Serializable object or string.
     * @param null   $ttl   Time to life measured in seconds.
     *
     * @return bool              True if the cache entry was set otherwise false.
     */
    public function set($key, $value, $ttl = null)
    {
        //		$ttl = $ttl ? $ttl : 480;
        if (!$this->global_cache instanceof ilGlobalCacheService || !$this->isActive()) {
            return false;
        }

        return $this->global_cache->set($key, $this->global_cache->serialize($value), $ttl);
    }


    /**
     * @param $key
     *
     * @return bool|mixed|null
     */
    public function get($key)
    {
        if (!$this->global_cache instanceof ilGlobalCacheService || !$this->isActive()) {
            return false;
        }
        $unserialized_return = $this->global_cache->unserialize($this->global_cache->get($key));

        if ($unserialized_return) {
            return $unserialized_return;
        }

        return null;
    }
}
