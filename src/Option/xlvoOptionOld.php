<?php

namespace LiveVoting\Option;

use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoOptionOld
 *
 * @package LiveVoting\Option
 *
 * @deprecated
 */
class xlvoOptionOld {

	use DICTrait;
	use LiveVotingTrait;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const TABLE_NAME = 'rep_robj_xlvo_option';
}
