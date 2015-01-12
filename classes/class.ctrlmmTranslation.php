<?php

/**
 * ctrlmmTranslation
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmTranslation extends ActiveRecord {

	const TABLE_NAME = 'ui_uihk_ctrlmm_t';

    /**
     * @var int
     *
     * @con_is_primary true
     * @con_sequence  true
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_is_notnull true
     * @con_length     8
     */
	protected $id = 0;

    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_is_notnull true
     * @con_length     8
     */
	protected $entry_id = 0;

    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_is_notnull true
     * @con_length     255
     */
	protected $language_key = '';

    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_is_notnull true
     * @con_length     500
     */
	protected $title = '';


    /**
     * @return string
     * @description Return the Name of your Database Table
     * @deprecated
     */
    static function returnDbTableName()
    {
        return self::TABLE_NAME;
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

        $result = self::where(array('entry_id'=>$entry_id, 'language_key'=>$language_key));

		if($result->hasSets()) {
			return $result->first();
		} else {
			$instace = new self();
			$instace->setLanguageKey($language_key);
			$instace->setEntryId($entry_id);

			return $instace;
		}
	}


	/**
	 * @param $entry_id
	 *
	 * @return mixed
	 */
	public static function _getAllTranslationsAsArray($entry_id) {
        $query =self::where(array('entry_id'=>$entry_id));

        $entries = $query->getArray();
        $return = array();
        foreach($entries as $set) {
            $return[$set['language_key']] = $set['title'];
        }

        return $return;
	}


	/**
	 * @param $entry_id
	 *
	 * @return bool|string
	 */
	public static function _getTitleForEntryId($entry_id) {
		global $ilUser;
		$obj = self::_getInstanceForLanguageKey($entry_id, $ilUser->getLanguage());

		if (!isset($obj)) {
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
        $result =self::where(array('entry_id'=>$entry_id));

        return $result->get();
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

}

?>
