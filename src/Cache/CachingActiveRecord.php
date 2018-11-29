<?php

namespace LiveVoting\Cache;

use ActiveRecord;
use arConnector;
use arConnectorDB;
use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class CachingActiveRecord
 *
 * @package LiveVoting\Cache
 * @author  nschaefli
 */
abstract class CachingActiveRecord extends ActiveRecord {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * CachingActiveRecord constructor.
	 *
	 * @param int              $primary_key
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
