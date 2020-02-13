<?php

namespace LiveVoting\Results;

use Closure;
use ilLiveVotingPlugin;
use LiveVoting\QuestionTypes\xlvoResultGUI;
use LiveVoting\User\xlvoParticipant;
use LiveVoting\User\xlvoParticipants;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoResults
 *
 * @package LiveVoting\Results
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoResults
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var int
     */
    protected $obj_id = 0;
    /**
     * @var int
     */
    protected $round_id = 0;


    /**
     * xlvoResults constructor.
     *
     * @param int $obj_id
     * @param int $round_id
     */
    public function __construct($obj_id, $round_id)
    {
        $this->obj_id = $obj_id;
        $this->round_id = $round_id;
    }


    /**
     * @param array|null    $filter
     * @param callable|null $formatParticipantCallable
     *
     * @param callable|null $concatVotesCallable
     *
     * @return array
     * @throws \Exception
     */
    public function getData(array $filter = null, callable $formatParticipantCallable = null, callable $concatVotesCallable = null)
    {
        if (!$formatParticipantCallable) {
            $formatParticipantCallable = $this->getFormatParticipantCallable();
        }

        if (!$concatVotesCallable) {
            $concatVotesCallable = $this->getConcatVotesCallable();
        }

        $obj_id = $this->getObjId();
        $votingRecords = xlvoVoting::where(array("obj_id" => $obj_id));
        if ($filter['voting']) {
            $votingRecords->where(array("id" => $filter['voting']));
        }
        if ($filter['voting_title']) {
            $votingRecords->where(array("id" => $filter['voting_title']));
        }
        /**
         * @var xlvoVoting[]      $votings
         * @var xlvoVoting        $voting
         * @var xlvoParticipant[] $participants
         */
        $votings = $votingRecords->get();
        $round_id = $this->getRoundId();
        $participants = xlvoParticipants::getInstance($obj_id)->getParticipantsForRound($round_id, $filter['participant']);
        $data = array();
        foreach ($participants as $participant) {
            foreach ($votings as $voting) {
                $votes = xlvoVote::where(array(
                    "round_id"        => $round_id,
                    "voting_id"       => $voting->getId(),
                    "user_id"         => $participant->getUserId(),
                    "user_identifier" => $participant->getUserIdentifier(),
                    "status"          => xlvoVote::STAT_ACTIVE,
                ))->get();
                $vote = array_shift(array_values($votes));
                $vote_ids = array_keys($votes);
                $data[] = array(
                    "position"        => (int) $voting->getPosition(),
                    "participant"     => $formatParticipantCallable($participant),
                    "user_id"         => $participant->getUserId(),
                    "user_identifier" => $participant->getUserIdentifier(),
                    "title"           => $voting->getTitle(),
                    "question"        => $voting->getRawQuestion(),
                    "answer"          => $concatVotesCallable($voting, $votes),
                    "answer_ids"      => $vote_ids,
                    "voting_id"       => $voting->getId(),
                    "round_id"        => $round_id,
                    "id"              => ($vote instanceof xlvoVote ? $vote->getId() : ''),
                );
            }
        }

        return $data;
    }


    /**
     * @return Closure
     */
    protected function getConcatVotesCallable()
    {
        return function (xlvoVoting $voting, $votes) {
            $resultsGUI = xlvoResultGUI::getInstance($voting);

            return $resultsGUI->getAPIRepresentation($votes);
        };
    }


    /**
     * @return Closure
     */
    protected function getFormatParticipantCallable()
    {
        return function (xlvoParticipant $participant) {
            return $participant->getUserIdentifier();
        };
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
     * @return int
     */
    public function getRoundId()
    {
        return $this->round_id;
    }


    /**
     * @param int $round_id
     */
    public function setRoundId($round_id)
    {
        $this->round_id = $round_id;
    }
}
