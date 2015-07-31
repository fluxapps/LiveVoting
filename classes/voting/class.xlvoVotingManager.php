<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingInterface.php');
require_once('./Services/Object/classes/class.ilObject2.php');

/**
 *
 */
class xlvoVotingManager implements xlvoVotingInterface
{
    /**
     * @var int
     */
    protected $obj_id;
    /**
     * @var ilUser
     */
    protected $user_ilias;

    /**
     *
     */
    public function __construct()
    {
        global $ilUser;

        /**
         * @var ilUser $ilUser
         */
        $this->user_ilias = $ilUser;
        $this->obj_id = ilObject2::_lookupObjId($_GET['ref_id']);
    }


    public function getVotings($obj_id = NULL)
    {
        $obj_id = $obj_id ? $obj_id : $this->obj_id;
        $xlvoVotings = xlvoVoting::where(array('obj_id' => $obj_id));
        return $xlvoVotings;
    }

    /**
     * @param $id
     * @return ActiveRecord|null
     */
    public function getVoting($id)
    {
        $xlvoVoting = xlvoVoting::find($id);
        if ($xlvoVoting instanceof xlvoVoting) {
            $xlvoOptions = $this->getOptionsForVoting($xlvoVoting->getId());
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
    public function getOptionsForVoting($voting_id, $only_active_options = true)
    {
        $xlvoOptions = xlvoOption::where(array('voting_id' => $voting_id));
        if ($only_active_options) $xlvoOptions = $xlvoOptions->where(array('status' => xlvoOption::STAT_ACTIVE));
        return $xlvoOptions;
    }

    public function getOption($option_id)
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
        if ($vote->getOptionId() == NULL) return false;
        /**
         * @var xlvoOption $xlvoOption
         */
        $xlvoOption = $this->getOption($vote->getOptionId());
        /**
         * @var xlvoVotingConfig $xlvoVotingConfig
         */
        $xlvoVotingConfig = $this->getVotingConfig();
        $xlvoVote = new xlvoVote();
        $xlvoVote->setOptionId($xlvoOption->getId());
        $xlvoVote->setType($xlvoOption->getType());
        $xlvoVote->setStatus(xlvoVote::STAT_ACTIVE);
        $xlvoVote->setUserIdType($xlvoVotingConfig->isAnonymous());
        switch ($xlvoVote->getUserIdType()) {
            case xlvoVote::USER_ILIAS:
                $xlvoVote->setUserId($this->user_ilias->getId());
                break;
            case xlvoVote::USER_ANONYMOUS:
                // TODO sessionId
                break;
        }
        $xlvoVote->create();
        return true;
    }


    /**
     * @param xlvoVote $vote
     *
     * @return bool
     */
    public function unvote(xlvoVote $vote)
    {
        if ($vote->getId() == NULL) return false;
        $vote->setStatus(xlvoVote::STAT_INACTIVE);
        $vote->update();
        return true;
    }

    /**
     * @return bool
     */
    public function deleteVotesForVoting($voting_id)
    {
        // TODO implement here
        return NULL;
    }

    public function getVotingConfig($obj_id = NULL)
    {
        $obj_id = $obj_id ? $obj_id : $this->obj_id;
        $xlvoVotingConfig = xlvoVotingConfig::find($obj_id);
        return $xlvoVotingConfig;
    }

}