<?php

require_once('./Services/ActiveRecord/class.ActiveRecord.php');

/**
 *
 */
class ilLiveVotingConfig extends ActiveRecord {

	/**
	 * @return string
	 */
	public static function returnDbTableName() {
		// TODO change name
		return 'sr_obj_config';
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
	protected $ref_id;
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
	protected $is_online;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $anonym;
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
	protected $start;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 */
	protected $end;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $freezed;


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
	public function getRefId() {
		return $this->ref_id;
	}


	/**
	 * @param int $ref_id
	 */
	public function setRefId($ref_id) {
		$this->ref_id = $ref_id;
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
	public function isIsOnline() {
		return $this->is_online;
	}


	/**
	 * @param boolean $is_online
	 */
	public function setIsOnline($is_online) {
		$this->is_online = $is_online;
	}


	/**
	 * @return boolean
	 */
	public function isAnonym() {
		return $this->anonym;
	}


	/**
	 * @param boolean $anonym
	 */
	public function setAnonym($anonym) {
		$this->anonym = $anonym;
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
	public function getStart() {
		return $this->start;
	}


	/**
	 * @param string $start
	 */
	public function setStart($start) {
		$this->start = $start;
	}


	/**
	 * @return string
	 */
	public function getEnd() {
		return $this->end;
	}


	/**
	 * @param string $end
	 */
	public function setEnd($end) {
		$this->end = $end;
	}


	/**
	 * @return boolean
	 */
	public function isFreezed() {
		return $this->freezed;
	}


	/**
	 * @param boolean $freezed
	 */
	public function setFreezed($freezed) {
		$this->freezed = $freezed;
	}
}