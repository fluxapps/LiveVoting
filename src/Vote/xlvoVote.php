<?php

namespace LiveVoting\Vote;

use LiveVoting\Cache\CachingActiveRecord;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoResultGUI;
use LiveVoting\User\xlvoUser;
use LiveVoting\User\xlvoVoteHistoryObject;
use LiveVoting\Voting\xlvoVoting;

/**
 * Class xlvoVote
 *
 * @package   LiveVoting\Vote
 * @author    Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author    Fabian Schmid <fs@studer-raimann.ch>
 * @author    Oskar Truffer <ot@studer-raimann.ch>
 * @version   1.0.0
 */
class xlvoVote extends CachingActiveRecord {

	const STAT_INACTIVE = 0;
	const STAT_ACTIVE = 1;
	const USER_ILIAS = 0;
	const USER_ANONYMOUS = 1;
	const TABLE_NAME = 'rep_robj_xlvo_vote_n';


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param xlvoUser $xlvoUser
	 * @param          $voting_id
	 * @param          $round_id
	 * @param null     $option_id
	 *
	 * @return string
	 */
	public static function vote(xlvoUser $xlvoUser, $voting_id, $round_id, $option_id = NULL) {
		$obj = self::getUserInstance($xlvoUser, $voting_id, $option_id);
		$obj->setStatus(self::STAT_ACTIVE);
		$obj->setRoundId($round_id);
		$obj->store();

		return $obj->getId();
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		return ($this->getStatus() == self::STAT_ACTIVE);
	}


	/**
	 * @param xlvoUser $xlvoUser
	 * @param          $voting_id
	 * @param null     $option_id
	 *
	 * @return string
	 */
	public static function unvote(xlvoUser $xlvoUser, $voting_id, $option_id = NULL) {
		$obj = self::getUserInstance($xlvoUser, $voting_id, $option_id);
		$obj->setStatus(self::STAT_INACTIVE);
		$obj->store();

		return $obj->getId();
	}


	/**
	 * @return xlvoOption
	 */
	public function getOption() {
		return xlvoOption::find($this->getOptionId());
	}


	/**
	 *
	 */
	public function update() {
		$this->setLastUpdate(time());
		parent::update();
	}


	/**
	 *
	 */
	public function create() {
		$this->setLastUpdate(time());
		parent::create();
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed
	 */
	public function sleep($field_name) {
		switch ($field_name) {
			case 'free_input':
				return preg_replace('/[\x{10000}-\x{10FFFF}]/u', "", $this->free_input);
		}

		return parent::sleep($field_name);
	}


	/**
	 * @param xlvoUser $xlvoUser
	 * @param int      $voting_id
	 * @param int      $round_id
	 * @param bool     $incl_inactive
	 *
	 * @return xlvoVote[]
	 */
	public static function getVotesOfUser(xlvoUser $xlvoUser, $voting_id, $round_id, $incl_inactive = false) {
		$where = array(
			'voting_id' => $voting_id,
			'status' => self::STAT_ACTIVE,
			'round_id' => $round_id,
		);
		if ($incl_inactive) {
			$where['status'] = array(
				self::STAT_INACTIVE,
				self::STAT_ACTIVE,
			);
		}
		if ($xlvoUser->isILIASUser()) {
			$where['user_id'] = $xlvoUser->getIdentifier();
		} else {
			$where['user_identifier'] = $xlvoUser->getIdentifier();
		}

		return self::where($where)->get();
	}


	/**
	 * @param xlvoUser $xlvoUser
	 * @param int      $voting_id
	 * @param int      $option_id
	 *
	 * @return xlvoVote
	 */
	protected static function getUserInstance(xlvoUser $xlvoUser, $voting_id, $option_id) {
		$where = array( 'voting_id' => $voting_id );
		if ($option_id) {
			$where = array( 'option_id' => $option_id );
		}
		if ($xlvoUser->isILIASUser()) {
			$where['user_id'] = $xlvoUser->getIdentifier();
		} else {
			$where['user_identifier'] = $xlvoUser->getIdentifier();
		}

		$vote = self::where($where)->first();

		if (!$vote instanceof self) {
			$vote = new self();
		}

		$vote->setUserIdType($xlvoUser->getType());
		if ($xlvoUser->isILIASUser()) {
			$vote->setUserId($xlvoUser->getIdentifier());
		} else {
			$vote->setUserIdentifier($xlvoUser->getIdentifier());
		}
		$vote->setOptionId($option_id);
		$vote->setVotingId($voting_id);

		return $vote;
	}


	/**
	 * @param xlvoUser $xlvoUser
	 * @param int      $voting_id
	 * @param int      $round_id
	 */
	public static function createHistoryObject($xlvoUser, $voting_id, $round_id) {
		$historyObject = new xlvoVoteHistoryObject();

		if ($xlvoUser->isILIASUser()) {
			$historyObject->setUserIdType(xlvoVote::USER_ILIAS);
			$historyObject->setUserId($xlvoUser->getIdentifier());
			$historyObject->setUserIdentifier(NULL);
		} else {
			$historyObject->setUserIdType(xlvoVote::USER_ANONYMOUS);
			$historyObject->setUserId(NULL);
			$historyObject->setUserIdentifier($xlvoUser->getIdentifier());
		}

		$historyObject->setVotingId($voting_id);
		$historyObject->setRoundId($round_id);
		$historyObject->setTimestamp(time());
		$gui = xlvoResultGUI::getInstance(xlvoVoting::find($voting_id));

		$votes = xlvoVote::where(array(
			'voting_id' => $voting_id,
			'status' => xlvoOption::STAT_ACTIVE,
			'round_id' => $round_id,
		));
		if ($xlvoUser->isILIASUser()) {
			$votes->where(array( "user_id" => $xlvoUser->getIdentifier() ));
		} else {
			$votes->where(array( "user_identifier" => $xlvoUser->getIdentifier() ));
		}
		$votes = $votes->get();
		$historyObject->setAnswer($gui->getTextRepresentation($votes));

		$historyObject->store();
	}


	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 * @db_is_primary       true
	 * @con_sequence        true
	 */
	protected $id;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $type;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $status;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $option_id;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $voting_id;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $user_id_type;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $user_identifier = 0;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $user_id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $last_update;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $round_id = 0;
	/**
	 * @var string
	 *
	 * @db_has_field true
	 * @db_fieldtype text
	 * @db_length    2000
	 */
	protected $free_input;


	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}


	/**
	 * @param int $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}


	/**
	 * @return int
	 */
	public function getOptionId() {
		return $this->option_id;
	}


	/**
	 * @param int $option_id
	 */
	public function setOptionId($option_id) {
		$this->option_id = $option_id;
	}


	/**
	 * @return int
	 */
	public function getVotingId() {
		return $this->voting_id;
	}


	/**
	 * @param int $voting_id
	 */
	public function setVotingId($voting_id) {
		$this->voting_id = $voting_id;
	}


	/**
	 * @return int
	 */
	public function getUserIdType() {
		return $this->user_id_type;
	}


	/**
	 * @param int $user_id_type
	 */
	public function setUserIdType($user_id_type) {
		$this->user_id_type = $user_id_type;
	}


	/**
	 * @return int
	 */
	public function getUserIdentifier() {
		return $this->user_identifier;
	}


	/**
	 * @param int $user_identifier
	 */
	public function setUserIdentifier($user_identifier) {
		$this->user_identifier = $user_identifier;
	}


	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}


	/**
	 * @return int
	 */
	public function getLastUpdate() {
		return $this->last_update;
	}


	/**
	 * @param int $last_update
	 */
	public function setLastUpdate($last_update) {
		$this->last_update = $last_update;
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


	/**
	 * @return string
	 */
	public function getFreeInput() {
		return $this->free_input;
	}


	/**
	 * @param string $free_input
	 */
	public function setFreeInput($free_input) {
		$this->free_input = $free_input;
	}
}
