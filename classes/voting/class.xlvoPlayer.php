<?php

require_once('./Services/ActiveRecord/class.ActiveRecord.php');

/**
 *
 */
class xlvoPlayer extends ActiveRecord {

	const STAT_STOPPED = 0;
	const STAT_RUNNING = 1;
	const STAT_START_VOTING = 2;
	const STAT_END_VOTING = 3;
	const RESET_OFF = 0;
	const RESET_ON = 1;


	/**
	 * @return string
	 */
	public static function returnDbTableName() {
		return 'rep_robj_xlvo_player';
	}


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
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $obj_id;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $active_voting;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $status;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $reset;


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
	public function getActiveVoting() {
		return $this->active_voting;
	}


	/**
	 * @param int $active_voting
	 */
	public function setActiveVoting($active_voting) {
		$this->active_voting = $active_voting;
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
	 * @return boolean
	 */
	public function isReset() {
		return $this->reset;
	}


	/**
	 * @param boolean $reset
	 */
	public function setReset($reset) {
		$this->reset = $reset;
	}
}