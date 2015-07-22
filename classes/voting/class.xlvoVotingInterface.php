<?php

/**
 *
 */
interface xlvoVotingInterface {

	/**
	 * @param xlvoVote $vote
	 *
	 * @return bool
	 */
	public function vote($vote);


	/**
	 * @param xlvoVote $vote
	 *
	 * @return bool
	 */
	public function unvote($vote);
}