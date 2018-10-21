<?php

namespace LiveVoting\User;

use LiveVoting\Cache\CachingActiveRecord;

/**
 * Class xlvoVoteHistory
 *
 * @package LiveVoting\Vote
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoVoteHistoryObject extends CachingActiveRecord {

	const TABLE_NAME = 'rep_robj_xlvo_votehist';


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
	 * @db_length           4
	 */
	protected $user_id_type;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $user_id;
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
	protected $voting_id;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $timestamp;
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
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           4000
	 */
	protected $answer = "";


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
	 * @return string
	 */
	public function getUserIdentifier() {
		return $this->user_identifier;
	}


	/**
	 * @param string $user_identifier
	 */
	public function setUserIdentifier($user_identifier) {
		$this->user_identifier = $user_identifier;
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
	public function getTimestamp() {
		return $this->timestamp;
	}


	/**
	 * @param int $timestamp
	 */
	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
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
	public function getAnswer() {
		return $this->answer;
	}


	/**
	 * @param string $answer
	 */
	public function setAnswer($answer) {
		$this->answer = $answer;
	}
}