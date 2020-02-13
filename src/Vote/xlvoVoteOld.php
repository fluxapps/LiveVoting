<?php

namespace LiveVoting\Vote;

use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoVoteOld
 *
 * @package LiveVoting\Vote
 *
 * @deprecated
 */
class xlvoVoteOld
{

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
    const TABLE_NAME = 'rep_robj_xlvo_vote';
}
