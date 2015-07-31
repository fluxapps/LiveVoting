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
	public function vote(xlvoVote $vote);


	/**
	 * @param xlvoVote $vote
	 *
	 * @return bool
	 */
	public function unvote(xlvoVote $vote);
}