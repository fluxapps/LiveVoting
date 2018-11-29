<?php

namespace LiveVoting\Voting;

use LiveVoting\Vote\xlvoVote;

/**
 * Interface xlvoVotingInterface
 *
 * @package LiveVoting\Voting
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 * TODO: Usage?
 */
interface xlvoVotingInterface {

	/**
	 * @param xlvoVote $vote
	 *
	 * @return bool
	 */
	public function vote(xlvoVote $vote);
}
