<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingInterface.php');

/**
 *
 */
class xlvoVotingManager implements xlvoVotingInterface
{

    /**
     *
     */
    public function __construct()
    {
    }


    public function getVotings()
    {

    }

    /**
     * @param $id
     * @return ActiveRecord|null
     */
    public function getVoting($id)
    {
        $xlvoVoting = xlvoVoting::find($id);
        if ($xlvoVoting instanceof xlvoVoting) {
            $xlvoOptions = $this->getVotingOptionsForVoting($xlvoVoting->getId());
            $xlvoVoting->setVotingOptions($xlvoOptions);
            return $xlvoVoting;
        } else return NULL;
    }


    /**
     * @param $voting_id
     * @param bool|true $only_active_options
     * @return $this|ActiveRecordList
     * @throws Exception
     */
    public function getVotingOptionsForVoting($voting_id, $only_active_options = true)
    {
        $xlvoOptions = xlvoOption::where(array('voting_id' => $voting_id));
        if ($only_active_options) $xlvoOptions = $xlvoOptions->where(array('status' => xlvoOption::STAT_ACTIVE));
        return $xlvoOptions;
    }

    public function getVotingOption($option_id)
    {
        $xlvoOption = xlvoOption::find($option_id);
        return $xlvoOption;
    }


    public function getVotes()
    {
        // TODO implement here
        return NULL;
    }

    /**
     * @param xlvoVote $vote
     *
     * @return bool
     */
    public function vote(xlvoVote $vote)
    {
        $xlvoOption = $vote->getOptionId();
        $xlvoVoting = $this->getVoting($xlvoOption);
        $xlvoVote = new xlvoVote();
        $xlvoVote->setOptionId($xlvoOption->getId());
        $xlvoVote->setStatus(xlvoVote::STAT_ACTIVE);
    }


    /**
     * @param xlvoVote $xlvoVote
     *
     * @return bool
     */
    public function unvote(xlvoVote $xlvoVote)
    {
        // TODO implement here
        return NULL;
    }

    /**
     * @return bool
     */
    public function deleteVotesForVoting($voting_id)
    {
        // TODO implement here
        return NULL;
    }

}