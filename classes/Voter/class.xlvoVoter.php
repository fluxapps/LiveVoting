<?php
require_once('./Services/ActiveRecord/class.ActiveRecord.php');

/**
 * Class xlvoVoter
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoVoter extends ActiveRecord {

	/**
	 * @return string
	 * @description Return the Name of your Database Table
	 * @deprecated
	 */
	static function returnDbTableName() {
		return 'xlvo_voter';
	}


	/**
	 * @param $user_identifier
	 * @param $player_id
	 */
	public static function register($user_identifier, $player_id) {
		$obj = self::where(array(
			'user_identifier' => $user_identifier,
			'player_id' => $player_id
		))->first();
		if (!$obj instanceof xlvoVoter) {
			$obj = new self();
			$obj->setUserIdentifier($user_identifier);
			$obj->setPlayerId($player_id);
			$obj->create();
		}
		$obj->setLastAccess(new DateTime());
		$obj->update();
	}


	/**
	 * @param $field_name
	 *
	 * @return mixed
	 */
	public function sleep($field_name) {
		if ($field_name == 'last_access') {
			return $this->last_access->getTimestamp();
		}
		return null;
	}


	/**
	 * @param $field_name
	 * @param $field_value
	 *
	 * @return mixed
	 */
	public function wakeUp($field_name, $field_value) {
		if ($field_name == 'last_access') {
			return new DateTime($field_value);
		}
		return null;
	}


	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $id;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $player_id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     128
	 */
	protected $user_identifier;
	/**
	 * @var DateTime
	 *
	 * @con_has_field  true
	 * @con_fieldtype  timestamp
	 */
	protected $last_access;


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
	public function getPlayerId() {
		return $this->player_id;
	}


	/**
	 * @param int $player_id
	 */
	public function setPlayerId($player_id) {
		$this->player_id = $player_id;
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
	 * @return DateTime
	 */
	public function getLastAccess() {
		return $this->last_access;
	}


	/**
	 * @param DateTime $last_access
	 */
	public function setLastAccess($last_access) {
		$this->last_access = $last_access;
	}
}