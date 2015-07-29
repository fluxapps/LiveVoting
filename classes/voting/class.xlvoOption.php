<?php

require_once('./Services/ActiveRecord/class.ActiveRecord.php');

/**
 *
 */
class xlvoOption extends ActiveRecord {

	const STAT_INACTIVE = 0;
	const STAT_ACTIVE = 1;


	/**
	 * @return string
	 */
	public static function returnDbTableName() {
		// TODO change back name
		return 'rep_robj_xlvo_option_n';
	}

	/*
	 * START
	 * xlvoSingleVoteOption
	 */
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $text;


	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}


	/**
	 * @param string $text
	 */
	public function setText($text) {
		$this->text = $text;
	}
	/*
	 * END
	 */

	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 * @db_is_primary       true
	 * @con_sequence        true
	 */
	protected $id;
	/**
	 * @var string
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
	 * @var xlvoVote []
	 */
	// TODO AR
	protected $votes;


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getVotingId() {
		return $this->voting_id;
	}


	/**
	 * @param string $voting_id
	 */
	public function setVotingId($voting_id) {
		$this->voting_id = $voting_id;
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
	 * @return xlvoVote[]
	 */
	public function getVotes() {
		return $this->votes;
	}


	/**
	 * @param xlvoVote[] $votes
	 */
	public function setVotes($votes) {
		$this->votes = $votes;
	}
}