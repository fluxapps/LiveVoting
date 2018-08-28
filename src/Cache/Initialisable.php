<?php

namespace LiveVoting\Cache;

/**
 * Interface Initialisable
 *
 * This interface must be implemented by all xlvoCache implementations.
 *
 * The main reason for this interface is to break the circular call
 * of the xlvoCache::getInstance method which requires the xlvoConf class, the xlvoConf
 * class extends the caching ar record which calls the xlvoCache::getInstance.
 *
 * The temporary solution is to create the xlvoCache in an "deactivated" state and initialize
 * the cache afterwards.
 *
 * @package LiveVoting\Cache
 * @author  Nicolas Schaefli <ns@studer-raimann.ch>
 *
 * @version 1
 */
interface Initialisable {

	/**
	 * Initialise the cache.
	 *
	 * @return void
	 * @internal
	 */
	public function init();
}
