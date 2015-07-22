<?php


/**
 * 
 */
public interface xlvoVotingInterface {

    /**
     * @param void $xlvoVote 
     * @return bool
     */
    public function vote($xlvoVote);

    /**
     * @param void $xlvoVote 
     * @return bool
     */
    public function unvote($xlvoVote);

}