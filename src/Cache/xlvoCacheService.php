<?php

namespace LiveVoting\Cache;

/**
 * Interface: xlvoCacheService
 *
 * @package LiveVoting\Cache
 *
 * Description: This interface defines the required method for the live voting caching service.
 *
 * User: Nicolas Schaefli <ns@studer-raimann.ch>
 * Date: 10/12/16
 * Time: 9:02 AM
 */
interface xlvoCacheService {

	/**
	 * Removes the data which is stored under the key given key.
	 * If the key is not found or the cache is not active, the method simply returns.
	 *
	 * @param string $key The key which should be removed from cache.
	 *
	 * @return void
	 */
	public function delete($key);


	/**
	 * Checks if the cache is active or not.
	 *
	 * @return bool
	 */
	public function isActive();


	/**
	 * Stores a new value in the cache.
	 * Already existent data will be overwritten.
	 *
	 * @param string $key   An unique key.
	 * @param mixed  $value Serializable object or string.
	 * @param null   $ttl   Time to life measured in seconds.
	 *
	 * @return bool              True if the cache entry was set otherwise false.
	 */
	public function set($key, $value, $ttl = NULL);


	/**
	 * Search the cached data with the help of the given key.
	 * This method returns false if the cache is not active and returns null if no data was found.
	 *
	 * @param string $key The key which should be used to fetch the data out of the cache.
	 *
	 * @return bool|mixed|null
	 */
	public function get($key);
}
