<?php

namespace LiveVoting\Voting;

use ActiveRecordList;
use ilException;
use ilLiveVotingPlugin;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\Exceptions\xlvoPlayerException;
use LiveVoting\Exceptions\xlvoVoterException;
use LiveVoting\Exceptions\xlvoVotingManagerException;
use LiveVoting\Option\xlvoOption;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Player\xlvoPlayer;
use LiveVoting\QuestionTypes\xlvoInputResultsGUI;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\Round\xlvoRound;
use LiveVoting\User\xlvoUser;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Vote\xlvoVote;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoVotingManager2
 *
 * @package LiveVoting\Voting
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoVotingManager2
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var xlvoVotingManager2[]
     */
    protected static $instances = array();
    /**
     * @var xlvoPlayer
     */
    protected $player;
    /**
     * @var xlvoVoting
     */
    protected $voting;
    /**
     * @var string
     */
    protected $pin = '';
    /**
     * @var int
     */
    protected $obj_id = 0;
    /**
     * @var ParamManager
     */
    protected $param_manager;


    /**
     * xlvoVotingManager2 constructor.
     *
     * @param $pin
     */
    public function __construct($pin)
    {
        if (!empty($pin)) {
            $this->initVoting($pin);
        }
    }


    private function initVoting($pin)
    {

        $this->setObjId(xlvoPin::checkPinAndGetObjId($pin));
        if ($this->getObjId() == 0) {
            throw new ilException("xlvoVotingManager2: Wrong PIN! - 2");
        }
        $this->setPlayer(xlvoPlayer::getInstanceForObjId($this->getObjId()));

        if ($voting_id = trim(filter_input(INPUT_GET, ParamManager::PARAM_VOTING), "/")) {
            $this->setVoting(xlvoVoting::findOrGetInstance($voting_id));
        } else {
            $this->setVoting(xlvoVoting::findOrGetInstance($this->player->getActiveVotingId()));
        }

        if ($this->getPlayer()->getRoundId() == 0) {
            $this->getPlayer()->setRoundId(xlvoRound::getLatestRoundId($this->getVoting()->getObjId()));
        }
    }


    /**
     * @param $pin
     *
     * @throws xlvoVoterException
     */
    public function checkPIN($pin)
    {
        xlvoPin::checkPinAndGetObjId($pin, true);
    }


    /**
     * @param $obj_id
     * @param $voting_id
     *
     * @return xlvoVotingManager2
     */
    public static function getInstanceFromObjId($obj_id)
    {
        if (!isset(self::$instances[$obj_id])) {
            /**
             * @var xlvoVotingConfig $xlvoVotingConfig
             */
            $xlvoVotingConfig = xlvoVotingConfig::findOrGetInstance($obj_id);

            self::$instances[$obj_id] = new self($xlvoVotingConfig->getPin());
        }

        return self::$instances[$obj_id];
    }


    /**
     * @param null $option
     */
    public function vote($option = null)
    {
        $xlvoOption = xlvoOption::findOrGetInstance($option);
        if ($this->hasUserVotedForOption($xlvoOption)) {
            $this->unvote($option);
        } else {
            $vote_id = xlvoVote::vote(xlvoUser::getInstance(), $this->getVoting()->getId(), $this->player->getRoundId(), $option);
        }
        if (!$this->getVoting()->isMultiSelection()) {
            $this->unvoteAll($vote_id);
        }

        $this->createHistoryObject();
    }


    /**
     *
     */
    public function prepare()
    {
        $this->getVoting()->regenerateOptionSorting();
        $this->getPlayer()->setStatus(xlvoPlayer::STAT_RUNNING);
        $this->getPlayer()->freeze();
    }


    /**
     * @param array $array ... => (input, vote_id)
     *
     * @throws xlvoVotingManagerException
     */
    public function inputAll(array $array)
    {
        foreach ($array as $item) {
            $this->input($item['input'], $item['vote_id']);
        }
        $this->createHistoryObject();
    }


    /**
     * @param      $input
     * @param      $vote_id
     *
     * @throws xlvoVotingManagerException
     */
    protected function input($input, $vote_id)
    {
        $options = $this->getOptions();
        $option = array_shift(array_values($options));
        if (!$option instanceof xlvoOption) {
            throw new xlvoVotingManagerException('No Option given');
        }
        /**
         * @var xlvoVote $xlvoVote
         */
        $xlvoVote = xlvoVote::find($vote_id);
        if (!$xlvoVote instanceof xlvoVote) {
            $xlvoVote = new xlvoVote();
        }
        $xlvoUser = xlvoUser::getInstance();
        if ($xlvoUser->getType() == xlvoUser::TYPE_ILIAS) {
            $xlvoVote->setUserId($xlvoUser->getIdentifier());
            $xlvoVote->setUserIdType(xlvoVote::USER_ILIAS);
        } else {
            $xlvoVote->setUserIdentifier($xlvoUser->getIdentifier());
            $xlvoVote->setUserIdType(xlvoVote::USER_ANONYMOUS);
        }
        $xlvoVote->setVotingId($this->getVoting()->getId());
        $xlvoVote->setOptionId($option->getId());
        $xlvoVote->setType(xlvoQuestionTypes::TYPE_FREE_INPUT);
        $xlvoVote->setStatus(xlvoVote::STAT_ACTIVE);
        $xlvoVote->setFreeInput($input);
        $xlvoVote->setRoundId(xlvoRound::getLatestRoundId($this->obj_id));
        $xlvoVote->store();
        if (!$this->getVoting()->isMultiFreeInput()) {
            $this->unvoteAll($xlvoVote->getId());
        }
    }


    /**
     * This is for when the admin adds a free text input. For normal user input use inputAll oder inputOne
     *
     * @param $input
     *
     * @return int
     * @throws xlvoVotingManagerException
     */
    public function addInput($input)
    {
        $options = $this->getOptions();
        $option = array_shift(array_values($options));
        if (!$option instanceof xlvoOption) {
            throw new xlvoVotingManagerException('No Option given');
        }
        $xlvoVote = new xlvoVote();
        $xlvoUser = xlvoUser::getInstance();
        if ($xlvoUser->getType() == xlvoUser::TYPE_ILIAS) {
            $xlvoVote->setUserId($xlvoUser->getIdentifier());
            $xlvoVote->setUserIdType(xlvoVote::USER_ILIAS);
        } else {
            $xlvoVote->setUserIdentifier($xlvoUser->getIdentifier());
            $xlvoVote->setUserIdType(xlvoVote::USER_ANONYMOUS);
        }
        $xlvoVote->setVotingId($this->getVoting()->getId());
        $xlvoVote->setOptionId($option->getId());
        $xlvoVote->setType(xlvoQuestionTypes::TYPE_FREE_INPUT);
        $xlvoVote->setStatus(xlvoVote::STAT_ACTIVE);
        $xlvoVote->setFreeInput($input);
        $xlvoVote->setRoundId(xlvoRound::getLatestRoundId($this->obj_id));
        $xlvoVote->create();

        return $xlvoVote->getId();
    }


    /**
     * @param $option_id
     *
     * @return int
     */
    public function countVotesOfOption($option_id)
    {
        return xlvoVote::where(array(
            'option_id' => $option_id,
            'status'    => xlvoVote::STAT_ACTIVE,
            'round_id'  => $this->player->getRoundId(),
        ))->count();
    }


    /**
     * @param $option_id
     *
     * @return xlvoVote[]
     */
    public function getVotesOfOption($option_id)
    {
        /**
         * @var xlvoVote[] $xlvoVotes
         */
        return xlvoVote::where(array(
            'option_id' => $option_id,
            'status'    => xlvoVote::STAT_ACTIVE,
            'round_id'  => $this->player->getRoundId(),
        ))->get();
    }


    /**
     * @param null $option
     */
    public function unvote($option = null)
    {
        xlvoVote::unvote(xlvoUser::getInstance(), $this->getVoting()->getId(), $option);
    }


    /**
     * @param null $except_vote_id
     */
    public function unvoteAll($except_vote_id = null)
    {
        foreach ($this->getVotesOfUser() as $xlvoVote) {
            if ($except_vote_id && $xlvoVote->getId() == $except_vote_id) {
                continue;
            }
            $xlvoVote->setStatus(xlvoVote::STAT_INACTIVE);
            $xlvoVote->store();
        }
    }


    /**
     * @return xlvoVote[]
     */
    public function getVotesOfUser($incl_inactive = false)
    {
        $xlvoVotes = xlvoVote::getVotesOfUser(xlvoUser::getInstance(), $this->getVoting()->getId(), $this->getPlayer()->getRoundId(), $incl_inactive);

        return $xlvoVotes;
    }


    /**
     * @param bool $incl_inactive
     *
     * @return xlvoVote
     */
    public function getFirstVoteOfUser($incl_inactive = false)
    {
        $xlvoVotes = $this->getVotesOfUser($incl_inactive);
        $xlvoVote = array_shift(array_values($xlvoVotes));

        return ($xlvoVote instanceof xlvoVote) ? $xlvoVote : new xlvoVote();
    }


    /**
     * @param xlvoOption $xlvoOption
     *
     * @return bool
     */
    public function hasUserVotedForOption(xlvoOption $xlvoOption)
    {
        $options = array();
        foreach ($this->getVotesOfUser() as $xlvoVote) {
            $options[] = $xlvoVote->getOptionId();
        }

        return in_array($xlvoOption->getId(), $options);
    }


    /**
     * @param $option_id
     *
     * @return array
     */
    public function getVotesOfUserOfOption($option_id)
    {
        $return = array();
        foreach ($this->getVotesOfUser() as $xlvoVote) {
            if ($xlvoVote->getOptionId() == $option_id) {
                $return[] = $xlvoVote;
            }
        }

        return $return;
    }


    /**
     * @param $voting_id
     */
    public function open($voting_id)
    {
        if ($this->getVotingsList()->where(array('id' => $voting_id))->hasSets()) {
            $this->player->setActiveVoting($voting_id);
            $this->player->store();
        }
    }


    /**
     *
     */
    public function previous()
    {
        if ($this->getVoting()->isFirst()) {
            return;
        }
        $prev_id = $this->getVotingsList('DESC')->where(array('position' => $this->voting->getPosition()), '<')->limit(0, 1)->getArray('id', 'id');
        $prev_id = array_shift(array_values($prev_id));
        $this->handleQuestionSwitching();

        $this->player->setActiveVoting($prev_id);
        $this->player->store();
        $this->getVoting()->regenerateOptionSorting();
    }


    /**
     *
     */
    public function next()
    {
        if ($this->getVoting()->isLast()) {
            return;
        }
        $next_id = $this->getVotingsList()->where(array('position' => $this->voting->getPosition()), '>')->limit(0, 1)->getArray('id', 'id');
        $next_id = array_shift(array_values($next_id));
        $this->handleQuestionSwitching();
        $this->player->setActiveVoting($next_id);
        $this->player->store();
        $this->getVoting()->regenerateOptionSorting();
    }


    /**
     *
     */
    public function terminate()
    {
        $this->player->terminate();
    }


    /**
     * @param $seconds
     */
    public function countdown($seconds)
    {
        $this->player->startCountDown($seconds);
    }


    /**
     *
     */
    public function attend()
    {
        $this->getPlayer()->attend();
    }


    /**
     * @return int
     */
    public function countVotings()
    {
        return $this->getVotingsList('ASC')->count();
    }


    /**
     * @return int
     */
    public function getVotingPosition()
    {
        $voting_position = 1;
        foreach ($this->getVotingsList('ASC')->getArray() as $key => $voting) {
            if ($this->getVoting()->getId() == $key) {
                break;
            }
            $voting_position++;
        }

        return $voting_position;
    }


    /**
     * @return int
     */
    public function countVotes()
    {
        return xlvoVote::where(array(
            'voting_id' => $this->getVoting()->getId(),
            'round_id'  => $this->getPlayer()->getRoundId(),
            'status'    => xlvoVote::STAT_ACTIVE,
        ))->count();
    }


    /**
     * @return int
     */
    public function countVoters()
    {
        $q = 'SELECT user_id_type, user_identifier, user_id FROM ' . xlvoVote::TABLE_NAME
            . ' WHERE voting_id = %s AND status = %s AND round_id = %s GROUP BY user_id_type, user_identifier, user_id';

        $res = self::dic()->database()->queryF($q, array('integer', 'integer', 'integer'), array(
            $this->getVoting()->getId(),
            xlvoVote::STAT_ACTIVE,
            $this->player->getRoundId(),
        ));

        return $res->numRows();
    }


    /**
     * @return int
     */
    public function getMaxCountOfVotes()
    {
        $q = "SELECT MAX(counted) AS maxcount FROM
				( SELECT COUNT(*) AS counted FROM " . xlvoVote::TABLE_NAME . " WHERE voting_id = %s AND status = %s AND round_id = %s GROUP BY option_id )
				AS counts";
        $res = self::dic()->database()->queryF($q, array('integer', 'integer', 'integer'), array(
            $this->getVoting()->getId(),
            xlvoVote::STAT_ACTIVE,
            $this->player->getRoundId(),
        ));
        $data = self::dic()->database()->fetchObject($res);

        return $data->maxcount ? $data->maxcount : 0;
    }


    /**
     * @return bool
     */
    public function hasVotes()
    {
        return ($this->countVotes() > 0);
    }


    /**
     *
     */
    public function reset()
    {
        $this->player->setButtonStates(array());
        $this->player->store();

        xlvoInputResultsGUI::getInstance($this)->reset();
    }


    /**
     * @return bool
     */
    public function canBeStarted()
    {
        return $this->getVotingsList()->hasSets();
    }


    /**
     * @return bool
     * @throws xlvoPlayerException
     */
    public function prepareStart()
    {
        if (!$this->getVotingConfig()->isObjOnline()) {
            throw new xlvoPlayerException('', xlvoPlayerException::OBJ_OFFLINE);
        }

        if ($this->canBeStarted()) {
            $xlvoVoting = $this->getVotingsList()->first();
            $this->getPlayer()->prepareStart($xlvoVoting->getId());

            return true;
        } else {
            throw new xlvoPlayerException('', xlvoPlayerException::NO_VOTINGS);
        }
    }


    /**
     * @return xlvoVotingConfig
     * @throws xlvoVotingManagerException
     */
    public function getVotingConfig()
    {
        /**
         * @var xlvoVotingConfig $xlvoVotingConfig
         */
        $xlvoVotingConfig = xlvoVotingConfig::find($this->obj_id);

        if ($xlvoVotingConfig instanceof xlvoVotingConfig) {
            $xlvoVotingConfig->setSelfVote((bool) $_GET['preview']);
            $xlvoVotingConfig->setKeyboardActive((bool) $_GET['key']);

            return $xlvoVotingConfig;
        } else {
            throw new xlvoVotingManagerException('Returned object is not an instance of xlvoVotingConfig.');
        }
    }


    /**
     * @param bool $order_by_free_input
     *
     * @return xlvoVote[]
     */
    public function getVotesOfVoting($order_by_free_input = false)
    {
        /**
         * @var xlvoVote[] $xlvoVotes
         */
        $where = xlvoVote::where(array(
            'voting_id' => $this->getVoting()->getId(),
            'status'    => xlvoOption::STAT_ACTIVE,
            'round_id'  => $this->player->getRoundId(),
        ));

        if ($order_by_free_input) {
            $where = $where->orderBy("free_input", "asc");
        }

        return $where->get();
    }


    /**
     * @return xlvoVoting[]
     */
    public function getAllVotings()
    {
        return $this->getVotingsList()->get();
    }


    /**
     * @param $option_id
     *
     * @return xlvoVote
     *
     * TODO: Usage?
     */
    public function getFirstVoteOfUserOfOption($option_id)
    {
        foreach ($this->getVotesOfUser() as $xlvoVote) {
            if ($xlvoVote->getOptionId() == $option_id) {
                return $xlvoVote;
            }
        }

        return null;
    }


    /**
     * @return int
     */
    public function countOptions()
    {
        return count($this->getOptions());
    }


    /**
     * @return xlvoOption[]
     */
    public function getOptions()
    {
        return $this->voting->getVotingOptions();
    }


    /**
     * @return xlvoPlayer
     */
    public function getPlayer()
    {
        return $this->player;
    }


    /**
     * @param xlvoPlayer $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }


    /**
     * @return xlvoVoting
     */
    public function getVoting()
    {
        return $this->voting;
    }


    /**
     * @param xlvoVoting $voting
     */
    public function setVoting($voting)
    {
        $this->voting = $voting;
    }


    /**
     * @return int
     */
    public function getObjId()
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId($obj_id)
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @param string $order
     *
     * @return ActiveRecordList
     */
    protected function getVotingsList($order = 'ASC')
    {
        return xlvoVoting::where(array(
            'obj_id'        => $this->getObjId(),
            'voting_status' => xlvoVoting::STAT_ACTIVE,
        ))->where(array('voting_type' => xlvoQuestionTypes::getActiveTypes()))->orderBy('position', $order);
    }


    /**
     * @throws xlvoVotingManagerException
     */
    public function handleQuestionSwitching()
    {
        switch ($this->getVotingConfig()->getResultsBehaviour()) {
            case xlvoVotingConfig::B_RESULTS_ALWAY_ON:
                $this->player->setShowResults(true);
                break;
            case xlvoVotingConfig::B_RESULTS_ALWAY_OFF:
                $this->player->setShowResults(false);
                break;
            case xlvoVotingConfig::B_RESULTS_REUSE:
                $this->player->setShowResults($this->player->isShowResults());
                break;
        }

        switch ($this->getVotingConfig()->getFrozenBehaviour()) {
            case xlvoVotingConfig::B_FROZEN_ALWAY_ON:
                $this->player->setFrozen(false);
                break;
            case xlvoVotingConfig::B_FROZEN_ALWAY_OFF:
                $this->player->setFrozen(true);
                break;
            case xlvoVotingConfig::B_FROZEN_REUSE:
                $this->player->setFrozen($this->player->isFrozen());
                break;
        }
    }


    /**
     * @param      $array
     *
     * @throws xlvoVotingManagerException
     */
    public function inputOne($array)
    {
        $this->inputAll(array($array));
    }


    /**
     * @throws xlvoVotingManagerException
     */
    protected function createHistoryObject()
    {
        if ($this->getVotingConfig()->getVotingHistory()) {
            xlvoVote::createHistoryObject(xlvoUser::getInstance(), $this->getVoting()->getId(), $this->player->getRoundId());
        }
    }
}
