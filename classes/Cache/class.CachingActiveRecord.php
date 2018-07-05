<?php

namespace LiveVoting\Cache;

use ActiveRecord;
use arConnector;
use arConnectorDB;

/**
 * Class CachingActiveRecord
 *
 * @author  nschaefli
 * @package LiveVoting\Cache
 */
abstract class CachingActiveRecord extends ActiveRecord {

	/**
	 * CachingActiveRecord constructor.
	 *
	 * @param int               $primary_key
	 * @param arConnector|NULL $connector
	 */
	public function __construct($primary_key = 0, arConnector $connector = NULL) {
		$arConnector = $connector;
		if (is_null($arConnector)) {
			$arConnector = new arConnectorCache(new arConnectorDB());
		}

		parent::__construct($primary_key, $arConnector);
	}
}
