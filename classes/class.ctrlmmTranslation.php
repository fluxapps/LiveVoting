<?php

/**
 * ctrlmmTranslation
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmTranslation {

	const TABLE_NAME = 'ui_uihk_ctrlmm_t';
	/**
	 * @var int
	 */
	protected $id = 0;
	protected $entry_id = 0;
	protected $language_key = '';
	protected $title = '';


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
		//		$this->updateDB();
		if ($id != 0) {
			$this->read();
		}
	}


	public function read() {
		$set = $this->db->query('SELECT * FROM ' . self::TABLE_NAME . ' ' . ' WHERE id = ' . $this->db->quote($this->getId(), 'integer'));
		while ($rec = $this->db->fetchObject($set)) {
			foreach ($this->getArrayForDb() as $k => $v) {
				$this->{$k} = $rec->{$k};
			}
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
	 * @param $entry_id
	 * @param $language_key
	 *
	 * @return ctrlmmTranslation
	 */
	public static function _getInstanceForLanguageKey($entry_id, $language_key) {
		global $ilDB;
		// Existing Object
		$set = $ilDB->query('SELECT id FROM ' . self::TABLE_NAME . ' ' . ' WHERE language_key = ' . $ilDB->quote($language_key, 'text')
			. ' AND entry_id = ' . $ilDB->quote($entry_id, 'integer'));
		while ($rec = $ilDB->fetchObject($set)) {
			return new self($rec->id);
		}
		$obj = new self();
		$obj->setLanguageKey($language_key);
		$obj->setEntryId($entry_id);

		return $obj;
	}


	/**
	 * @param $entry_id
	 *
	 * @return mixed
	 */
	public static function _getAllTranslationsAsArray($entry_id) {
		global $ilDB;
		$return = array();
		$set = $ilDB->query('SELECT language_key, title FROM ' . self::TABLE_NAME . ' WHERE entry_id = ' . $ilDB->quote($entry_id, 'integer'));
		while ($rec = $ilDB->fetchObject($set)) {
			$return[$rec->language_key] = $rec->title;
		}

		return $return;
	}


	/**
	 * @param $entry_id
	 *
	 * @return bool|string
	 */
	public static function _getTitleForEntryId($entry_id) {
		global $ilDB, $ilUser;
		$obj = self::_getInstanceForLanguageKey($entry_id, $ilUser->getLanguage());
		if ($obj->getId() == 0) {
			require_once('./Services/Language/classes/class.ilLanguage.php');
			$lngs = new ilLanguage('en');
			$obj = self::_getInstanceForLanguageKey($entry_id, $lngs->getDefaultLanguage());
			if ($obj->getId() == 0) {
				return false;
			}
		}

		return $obj->getTitle();
	}


	/**
	 * @param $entry_id
	 *
	 * @return ctrlmmTranslation[]
	 */
	public function _getAllInstancesForEntryId($entry_id) {
		global $ilDB;
		$return = array();
		$set = $ilDB->query('SELECT id FROM ' . self::TABLE_NAME . ' WHERE entry_id = ' . $ilDB->quote($entry_id, 'integer'));
		while ($rec = $ilDB->fetchObject($set)) {
			$return[] = new self($rec->id);
		}

		return $return;
	}


	public static function _deleteAllInstancesForEntryId($entry_id) {
		foreach (self::_getAllInstancesForEntryId($entry_id) as $tr) {
			$tr->delete();
		}
	}


	//
	// Setter & Getter
	//
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
	 * @param int $entry_id
	 */
	public function setEntryId($entry_id) {
		$this->entry_id = $entry_id;
	}


	/**
	 * @return int
	 */
	public function getEntryId() {
		return $this->entry_id;
	}


	/**
	 * @param string $language_key
	 */
	public function setLanguageKey($language_key) {
		$this->language_key = $language_key;
	}


	/**
	 * @return string
	 */
	public function getLanguageKey() {
		return $this->language_key;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
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
