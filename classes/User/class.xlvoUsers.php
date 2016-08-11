<?php

class xlvoUsers {

	/**
	 * @var xlvoUsers[]
	 */
	protected static $instances = array();

	protected $obj_id;

	protected function __construct($obj_id) {
		$this->obj_id = $obj_id;
	}

	/**
	 * @param $obj_id
	 * @return xlvoUsers
	 */
	public static function getInstance($obj_id) {
		if(!self::$instances[$obj_id])
			self::$instances[$obj_id] = new xlvoUsers($obj_id);
		return self::$instances[$obj_id];
	}

	/**
	 * @param $round_id
	 * @return array user_id_type, user_identifier, user_id
	 */
	public function getUsersForRound($round_id) {
		global $ilDB;

		$query = "SELECT DISTINCT user_identifier, user_id FROM rep_robj_xlvo_vote_n WHERE round_id = %s";
		$result = $ilDB->queryF($query, array("integer"), array($round_id));
		$rows = array();
		$i = 1;
		while($row = $ilDB->fetchAssoc($result)) {
			$row['number'] = $i;
			$rows[] = $row;
			$i++;
		}
		return $rows;
	}
}