<?php
namespace LiveVoting\Results;

use LiveVoting\Voting\xlvoVoting;
use LiveVoting\User\xlvoParticipants;
use LiveVoting\User\xlvoParticipant;
use LiveVoting\User\xlvoUser;
use LiveVoting\Vote\xlvoVote;
use xlvoResultGUI;

/**
 * Class xlvoResults
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoResults {

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
	public function __construct($obj_id, $round_id) {
		$this->obj_id = $obj_id;
		$this->round_id = $round_id;
	}


	/**
	 * @param array|null $filter
	 * @param callable|null $format_patricipant_name
	 * @return array
	 */
	public function getData(array $filter = null, callable $format_patricipant_name = null, callable $concat_votes = null) {
		if (!$format_patricipant_name) {
			$format_patricipant_name = $this->getFormatParticipantCallable();
		}

		if (!$concat_votes) {
			$concat_votes = $this->getConcatVotesCallable();
		}

		$obj_id = $this->getObjId();
		$votingRecords = xlvoVoting::where(array( "obj_id" => $obj_id ));
		if ($filter['voting']) {
			$votingRecords->where(array( "id" => $filter['voting'] ));
		}
		if ($filter['voting_title']) {
			$votingRecords->where(array( "id" => $filter['voting_title'] ));
		}
		/**
		 * @var $votings      xlvoVoting[]
		 * @var $voting       xlvoVoting
		 * @var $participants xlvoParticipant[]
		 */
		$votings = $votingRecords->get();
		$round_id = $this->getRoundId();
		$participants = xlvoParticipants::getInstance($obj_id)
		                                ->getParticipantsForRound($round_id, $filter['participant']);
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
				$data[] = array(
					"position"        => $voting->getPosition(),
					"participant"     => $format_patricipant_name($participant),
					"user_id"         => $participant->getUserId(),
					"user_identifier" => $participant->getUserIdentifier(),
					"title"           => $voting->getTitle(),
					"question"        => $voting->getQuestion(),
					"answer"          => $concat_votes($voting, $votes),
					"voting_id"       => $voting->getId(),
					"round_id"        => $round_id,
				);
			}
		}

		return $data;
	}


	/**
	 * @return \Closure
	 */
	protected function getConcatVotesCallable() {
		return function (xlvoVoting $voting, $votes) {
			$resultsGUI = xlvoResultGUI::getInstance($voting);

			return $resultsGUI->getAPIRepresentation($votes);
		};
	}


	/**
	 * @return \Closure
	 */
	protected function getFormatParticipantCallable() {
		return function (xlvoParticipant $participant) {
			if ($participant->getUserIdType() == xlvoUser::TYPE_ILIAS
			    && $participant->getUserId()
			) {
				$name = \ilObjUser::_lookupName($participant->getUserId());

				return $name['firstname'] . " " . $name['lastname'];
			}

			return $participant->getUserIdentifier();
		};
	}


	/**
	 * @return int
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}


	/**
	 * @return int
	 */
	public function getRoundId() {
		return $this->round_id;
	}


	/**
	 * @param int $round_id
	 */
	public function setRoundId($round_id) {
		$this->round_id = $round_id;
	}
}