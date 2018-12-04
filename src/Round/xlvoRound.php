<?php

namespace LiveVoting\Round;

use LiveVoting\Cache\CachingActiveRecord;

/**
 * Class xlvoRound
 *
 * @package LiveVoting\Round
 * @author  : Oskar Truffer <ot@studer-raimann.ch>
 *
 * A voting can go for several rounds. This active Record tracks these rounds
 *
 */
class xlvoRound extends CachingActiveRecord {

	const TABLE_NAME = 'rep_robj_xlvo_round_n';


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
	 * @param $obj_id
	 *
	 * @return int
	 */
	public static function getLatestRoundId($obj_id) {
		$q = "SELECT result.id FROM (SELECT id FROM " . self::TABLE_NAME . " WHERE " . self::TABLE_NAME
			. ".obj_id = %s) AS result ORDER BY result.id DESC LIMIT 1";
		//$q = "SELECT MAX(id) FROM " . self::TABLE_NAME . " WHERE obj_id = %s";
		$result = self::dic()->database()->queryF($q, array( 'integer' ), array( $obj_id ));
		$data = self::dic()->database()->fetchObject($result);

		if (!isset($data->id)) {
			$round = self::createFirstRound($obj_id);

			return $round->getId();
		}

		return $data->id;
	}


	/**
	 * Gets you the latest round for this object. creates the first one if there is no round yet.
	 *
	 * @param $obj_id int
	 *
	 * @return xlvoRound
	 */
	public static function getLatestRound($obj_id) {
		return xlvoRound::find(self::getLatestRoundId($obj_id));
	}


	/**
	 * @param $obj_id int
	 *
	 * @return xlvoRound
	 */
	public static function createFirstRound($obj_id) {
		$round = new xlvoRound();
		$round->setRoundNumber(1);
		$round->setObjId($obj_id);
		$round->store();

		return $round;
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
	protected $round_number;
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
	public function getRoundNumber() {
		return $this->round_number;
	}


	/**
	 * @param int $round_number
	 */
	public function setRoundNumber($round_number) {
		$this->round_number = $round_number;
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
