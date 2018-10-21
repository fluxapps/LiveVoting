<?php

namespace LiveVoting\Conf;

use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class xlvoConfOld
 *
 * @package LiveVoting\Conf
 * @deprecated
 */
class xlvoConfOld {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	/**
	 * @var string
	 * @deprecated
	 */
	const TABLE_NAME = 'rep_robj_xlvo_conf';
}
