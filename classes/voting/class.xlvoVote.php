<?php

require_once('./Services/ActiveRecord/class.ActiveRecord.php');

/**
 *
 */
class xlvoVote extends ActiveRecord {

	/**
	 * @return string
	 */
	public static function returnDbTableName() {
		// TODO change back name
		return 'rep_robj_xlvo_vote_n';
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
	protected $option_id;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $user_id_type;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
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
}