<?php

/**
 * ctrlmmData
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmData {

	const TABLE_NAME = 'ui_uihk_ctrlmm_d';
	/**
	 * @var int
	 */
	public $id = 0;
	/**
	 * @var int
	 */
	public $parent_id = 0;
	/**
	 * @var string
	 */
	public $data_key = '';
	/**
	 * @var string
	 */
	public $data_value = '';


	/**
	 * @param $id
	 */
	function __construct($id = 0) {
		global $ilDB;
		/**
		 * @var $ilDB ilDB
		 */
		$this->id = $id;
		$this->db = $ilDB;
		if ($id != 0) {
			$this->read();
		}
	}


	public function read() {
		if (ctrlmmMenu::checkGlobalCache()) {
			$rec = ilGlobalCache::getInstance('ctrl_mm')->get(self::TABLE_NAME . '_' . $this->getId());
			if (! $rec) {
				$set = $this->db->query('SELECT * FROM ' . self::TABLE_NAME . ' ' . ' WHERE id = ' . $this->db->quote($this->getId(), 'integer'));
				$rec = $this->db->fetchObject($set);
				ilGlobalCache::getInstance('ctrl_mm')->set(self::TABLE_NAME . '_' . $this->getId(), $rec, 60);
			}
		} else {
			$set = $this->db->query('SELECT * FROM ' . self::TABLE_NAME . ' ' . ' WHERE id = ' . $this->db->quote($this->getId(), 'integer'));
			$rec = $this->db->fetchObject($set);
		}

		foreach ($this->getArrayForDb() as $k => $v) {
			$this->{$k} = $rec->{$k};
		}
	}


	/**
	 * @return array
	 */
	public function getArrayForDb() {
		$e = array();
		foreach (get_object_vars($this) as $k => $v) {
			if (! in_array($k, array( 'db' ))) {
				$e[$k] = array( self::_getType($v), $this->$k );
			}
		}

		return $e;
	}


	final function initDB() {
		foreach ($this->getArrayForDb() as $k => $v) {
			$fields[$k] = array(
				'type' => $v[0],
			);
			switch ($v[0]) {
				case 'integer':
					$fields[$k]['length'] = 4;
					break;
				case 'text':
					$fields[$k]['length'] = 1024;
					break;
			}
			if ($k == 'id') {
				$fields[$k]['notnull'] = true;
			}
		}
		if (! $this->db->tableExists(self::TABLE_NAME)) {
			$this->db->createTable(self::TABLE_NAME, $fields);
			$this->db->addPrimaryKey(self::TABLE_NAME, array( 'id' ));
			$this->db->createSequence(self::TABLE_NAME);
		}
	}


	final function updateDB() {
		if (! $this->db->tableExists(self::TABLE_NAME)) {
			$this->initDB();

			return true;
		}
		foreach ($this->getArrayForDb() as $k => $v) {
			if (! $this->db->tableColumnExists(self::TABLE_NAME, $k)) {
				$field = array(
					'type' => $v[0],
				);
				switch ($v[0]) {
					case 'integer':
						$field['length'] = 4;
						break;
					case 'text':
						$field['length'] = 1024;
						break;
				}
				if ($k == 'id') {
					$field['notnull'] = true;
				}
				$this->db->addTableColumn(self::TABLE_NAME, $k, $field);
			}
		}
	}


	final private function resetDB() {
		$this->db->dropTable(self::TABLE_NAME);
		$this->initDB();
	}


	public function create() {
		if ($this->getId() != 0) {
			$this->update();

			return true;
		}
		$this->setId($this->db->nextID(self::TABLE_NAME));
		$this->db->insert(self::TABLE_NAME, $this->getArrayForDb());
	}


	/**
	 * @return int
	 */
	public function delete() {
		return $this->db->manipulate('DELETE FROM ' . self::TABLE_NAME . ' WHERE id = ' . $this->db->quote($this->getId(), 'integer'));
	}


	public function update() {
		if ($this->getId() == 0) {
			$this->create();

			return true;
		}
		$this->db->update(self::TABLE_NAME, $this->getArrayForDb(), array(
			'id' => array(
				'integer',
				$this->getId()
			),
		));
	}


	//
	// Static
	//
	/**
	 * @param $parent_id
	 * @param $data_key
	 *
	 * @return ctrlmmData
	 */
	public static function _getInstanceForDataKey($parent_id, $data_key) {
		global $ilDB;
		$set = $ilDB->query('SELECT * FROM ' . self::TABLE_NAME . ' ' . ' WHERE parent_id = ' . $ilDB->quote($parent_id, 'integer')
			. ' AND data_key = ' . $ilDB->quote($data_key, 'text'));
		while ($rec = $ilDB->fetchObject($set)) {
			return new self($rec->id);
		}
		$obj = new self();
		$obj->setParentId($parent_id);
		$obj->setDataKey($data_key);

		return $obj;
	}


	/**
	 * @param      $parent_id
	 * @param bool $as_array
	 *
	 * @return ctrlmmData[]
	 */
	public static function _getAllInstancesForParentId($parent_id, $as_array = false) {
		global $ilDB;
		$set = $ilDB->query('SELECT * FROM ' . self::TABLE_NAME . ' ' . ' WHERE parent_id = ' . $ilDB->quote($parent_id, 'integer'));
		$return = array();
		while ($rec = $ilDB->fetchObject($set)) {
			if ($as_array) {
				$return[] = (array)new self($rec->id);
			} else {
				$return[] = new self($rec->id);
			}
		}

		return $return;
	}


	public static function _deleteAllInstancesForParentId($parent_id) {
		foreach (self::_getAllInstancesForParentId($parent_id) as $da) {
			$da->delete();
		}
	}


	/**
	 * @param $parent_id
	 *
	 * @return array
	 */
	public static function getDataForEntry($parent_id) {
		$data = array();
		foreach (self::_getAllInstancesForParentId($parent_id) as $d) {
			$data[$d->getDataKey()] = $d->getDataValue();
		}

		return $data;
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
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $data_key
	 */
	public function setDataKey($data_key) {
		$this->data_key = $data_key;
	}


	/**
	 * @return string
	 */
	public function getDataKey() {
		return $this->data_key;
	}


	/**
	 * @param string $data_value
	 */
	public function setDataValue($data_value) {
		$this->data_value = $data_value;
	}


	/**
	 * @return string
	 */
	public function getDataValue() {
		return $this->data_value;
	}


	/**
	 * @param int $parent_id
	 */
	public function setParentId($parent_id) {
		$this->parent_id = $parent_id;
	}


	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->parent_id;
	}


	//
	// Helper
	//
	/**
	 * @param $var
	 *
	 * @return string
	 */
	public static function _getType($var) {
		switch (gettype($var)) {
			case 'string':
			case 'array':
			case 'object':
				return 'text';
			case 'NULL':
			case 'boolean':
				return 'integer';
			default:
				return gettype($var);
		}
	}
}

?>
