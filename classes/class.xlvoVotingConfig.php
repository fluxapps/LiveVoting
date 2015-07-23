<?php

require_once('./Services/ActiveRecord/class.ActiveRecord.php');

/**
 *
 */
class xlvoVotingConfig extends ActiveRecord {

	/**
	 * @return string
	 */
	public static function returnDbTableName() {
		return 'rep_robj_xlvo_config';
	}

	/**
	 * @var int
	 *
	 * @db_is_primary       true
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
	protected $pin;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $obj_online;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $anonymous;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $terminable;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 */
	protected $start_date;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 */
	protected $end_date;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $frozen;


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
	public function getPin() {
		return $this->pin;
	}


	/**
	 * @param int $pin
	 */
	public function setPin($pin) {
		$this->pin = $pin;
	}


	/**
	 * @return boolean
	 */
	public function isObjOnline() {
		return $this->obj_online;
	}


	/**
	 * @param boolean $obj_online
	 */
	public function setObjOnline($obj_online) {
		$this->obj_online = $obj_online;
	}


	/**
	 * @return boolean
	 */
	public function isAnonymous() {
		return $this->anonymous;
	}


	/**
	 * @param boolean $anonymous
	 */
	public function setAnonymous($anonymous) {
		$this->anonymous = $anonymous;
	}


	/**
	 * @return boolean
	 */
	public function isTerminable() {
		return $this->terminable;
	}


	/**
	 * @param boolean $terminable
	 */
	public function setTerminable($terminable) {
		$this->terminable = $terminable;
	}


	/**
	 * @return string
	 */
	public function getStartDate() {
		return $this->start_date;
	}


	/**
	 * @param string $start_date
	 */
	public function setStartDate($start_date) {
		$this->start_date = $start_date;
	}


	/**
	 * @return string
	 */
	public function getEndDate() {
		return $this->end_date;
	}


	/**
	 * @param string $end_date
	 */
	public function setEndDate($end_date) {
		$this->end_date = $end_date;
	}


	/**
	 * @return boolean
	 */
	public function isFrozen() {
		return $this->frozen;
	}


	/**
	 * @param boolean $frozen
	 */
	public function setFrozen($frozen) {
		$this->frozen = $frozen;
	}
}