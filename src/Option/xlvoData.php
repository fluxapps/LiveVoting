<?php

namespace LiveVoting\Option;

use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class xlvoData
 *
 * @package LiveVoting\Option
 *
 * @deprecated
 */
class xlvoData {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	/**
	 * @var string
	 * @deprecated
	 */
	const TABLE_NAME = 'rep_robj_xlvo_data';
}
