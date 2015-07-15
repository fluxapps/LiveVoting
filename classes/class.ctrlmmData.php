<?php

/**
 * ctrlmmData
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmData extends ActiveRecord {

	const TABLE_NAME = 'ui_uihk_ctrlmm_d';

	const DATA_TYPE_STRING = 'str';
	const DATA_TYPE_ARRAY = 'arr';

	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_sequence   true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	public $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	public $parent_id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     1024
	 */
	public $data_key = '';
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     1024
	 */
	public $data_value = '';


	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     10
	 */
	public $data_type = DATA_TYPE_STRING;


	/**
	 * @return string
	 * @description Return the Name of your Database Table
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
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
		$result = self::where(array( 'parent_id' => $parent_id, 'data_key' => $data_key ));

		if ($result->hasSets()) {
			return $result->first();
		} else {
			$instance = new self();
			$instance->setParentId($parent_id);
			$instance->setDataKey($data_key);

			return $instance;
		}
	}


	/**
	 * @param      $parent_id
	 * @param bool $as_array
	 *
	 * @return ctrlmmData[]|array
	 */
	public static function _getAllInstancesForParentId($parent_id, $as_array = false) {
		$result = self::where(array( 'parent_id' => $parent_id ));

		if ($as_array) {
			return $result->getArray();
		} else {
			return $result->get();
		}
	}


	public static function _deleteAllInstancesForParentId($parent_id) {
		$deleteChilds = self::_getAllInstancesForParentId($parent_id);
		foreach ($deleteChilds as $nr => $child) {
			$child->delete();
		}
	}

	public static function _getDataTypeForValue($value) {
		return (is_array($value))? self::DATA_TYPE_ARRAY : self::DATA_TYPE_STRING;
	}

	/**
	 * @param $parent_id
	 *
	 * @return array
	 */
	public static function getDataForEntry($parent_id) {
		$sets = self::_getAllInstancesForParentId($parent_id);

		$data = array();
		foreach ($sets as $set) {
			if($set->getDataType() == self::DATA_TYPE_ARRAY) {
				$data[$set->getDataKey()] = json_decode($set->getDataValue(), true);
			} else {
				$data[$set->getDataKey()] = $set->getDataValue();
			}
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

	/**
	 * @return string
	 */
	public function getDataType() {
		return $this->data_type;
	}


	/**
	 * @param string $data_type
	 */
	public function setDataType($data_type) {
		$this->data_type = $data_type;
	}
}

?>
