<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * Class xlvoRound
 *
 * @author: Oskar Truffer <ot@studer-raimann.ch>
 *
 * A voting can go for several rounds. This active Record tracks these rounds
 *
 */
class xlvoRound extends ActiveRecord {

	/**
	 * Gets you the latest round for this object. creates the first one if there is no round yet.
	 *
	 * @param $obj_id int
	 * @return xlvoRound
	 */
	public static function getLatestRound($obj_id) {
		$latestRound = xlvoRound::where(array("obj_id" => $obj_id))->orderBy("number")->last();
		if(!$latestRound)
			$latestRound = self::createFirstRound($obj_id);
		return $latestRound;
	}

	/**
	 * @param $obj_id int
	 * @return xlvoRound
	 */
	public static function createFirstRound($obj_id) {
		$round = new xlvoRound();
		$round->setNumber(1);
		$round->setObjId($obj_id);
		$round->create();
		return $round;
	}

	/**
	 * @return string
	 */
	public static function returnDbTableName() {
		return 'rep_robj_xlvo_round_n';
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
	protected $number;

	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $title;

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
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @param int $number
	 */
	public function setNumber($number) {
		$this->number = $number;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
}