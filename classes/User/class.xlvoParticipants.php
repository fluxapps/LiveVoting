<?php

namespace LiveVoting\User;

class xlvoParticipants {

	/**
	 * @var xlvoParticipants[]
	 */
	protected static $instances = array();
	/**
	 * @var int
	 */
	protected $obj_id;


	/**
	 * xlvoParticipants constructor.
	 *
	 * @param $obj_id
	 */
	protected function __construct($obj_id) {
		$this->obj_id = $obj_id;
	}


	/**
	 * @param $obj_id
	 * @return xlvoParticipants
	 */
	public static function getInstance($obj_id) {
		if (!self::$instances[$obj_id]) {
			self::$instances[$obj_id] = new xlvoParticipants($obj_id);
		}

		return self::$instances[$obj_id];
	}


	/**
	 * @param $round_id int
	 * @param $filter   string what's the participant id or identifier you're looking for?
	 * @return xlvoParticipant[]
	 */
	public function getParticipantsForRound($round_id, $filter = null) {
		global $DIC;
		$ilDB = $DIC->database();

		if ($filter) {
			$query = "SELECT DISTINCT user_identifier, user_id FROM " . \LiveVoting\Vote\xlvoVote::TABLE_NAME . " WHERE round_id = %s AND (user_identifier LIKE %s OR user_id = %s)";
			$result = $ilDB->queryF($query, array( "integer", "text", "integer" ), array( $round_id, $filter, $filter ));
		} else {
			$query = "SELECT DISTINCT user_identifier, user_id FROM " . \LiveVoting\Vote\xlvoVote::TABLE_NAME . " WHERE round_id = %s";
			$result = $ilDB->queryF($query, array( "integer" ), array( $round_id ));
		}

		$rows = array();
		$i = 0;
		while ($row = $ilDB->fetchAssoc($result)) {
			$i ++;
			if ($filter && $row['user_id'] != $filter && $row['user_identifier'] != $filter) {
				continue;
			}
			$user = new xlvoParticipant();
			$user->setNumber($i);
			$user->setUserId($row['user_id']);
			$user->setUserIdentifier($row['user_identifier']);
			$user->setUserIdType($row['user_id'] ? xlvoUser::TYPE_ILIAS : xlvoUser::TYPE_PIN);
			$rows[] = $user;
		}

		return $rows;
	}
}