<?php

namespace LiveVoting\Option;

use LiveVoting\Cache\CachingActiveRecord;

/**
 * Class xlvoOption
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoOption extends CachingActiveRecord {

	const STAT_INACTIVE = 0;
	const STAT_ACTIVE = 1;


	/**
	 * @return string
	 */
	public static function returnDbTableName() {
		return 'rep_robj_xlvo_option_n';
	}


	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $text;


	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}


	/**
	 * @return string
	 */
	public function getTextForPresentation() {
		return \ilUtil::prepareTextareaOutput($this->getTextForEditor(), true);
	}


	/**
	 * @return string
	 */
	public function getTextForEditor() {
		return \ilRTE::_replaceMediaObjectImageSrc($this->text, 1);
	}


	/**
	 * @param string $text
	 */
	public function setText($text) {
		$this->text = $text;
	}


	public function store() {
		if (self::where(array( 'id' => $this->getId() ))->hasSets()) {
			$this->update();
		} else {
			$this->create();
		}
	}


	/**
	 * @return string
	 */
	public function getCipher() {
		return chr($this->getPosition() + 64);
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
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $voting_id;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $type;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $status = self::STAT_ACTIVE;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $position;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $correct_position = null;


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
	 * @return string
	 */
	public function getVotingId() {
		return $this->voting_id;
	}


	/**
	 * @param string $voting_id
	 */
	public function setVotingId($voting_id) {
		$this->voting_id = $voting_id;
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}


	/**
	 * @param int $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}


	/**
	 * @return int
	 */
	public function getPosition() {
		return $this->position;
	}


	/**
	 * @param int $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}


	/**
	 * @return string
	 */
	public function getCorrectPosition() {
		return $this->correct_position;
	}


	/**
	 * @param string $correct_position
	 */
	public function setCorrectPosition($correct_position) {
		$this->correct_position = $correct_position;
	}


	/**
	 * @return \stdClass
	 */
	public function _toJson() {
		$class = new \stdClass();
		$class->Id = (int)$this->getId();
		$class->Text = (string)$this->getText();
		$class->Position = (int)$this->getPosition();

		return $class;
	}
}