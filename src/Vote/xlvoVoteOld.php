<?php

namespace LiveVoting\Vote;

use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class xlvoVoteOld
 *
 * @package LiveVoting\Vote
 *
 * @deprecated
 */
class xlvoVoteOld {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	/**
	 * @var string
	 * @deprecated
	 */
	const TABLE_NAME = 'rep_robj_xlvo_vote';
}
