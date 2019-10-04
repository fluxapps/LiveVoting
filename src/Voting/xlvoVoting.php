<?php

namespace LiveVoting\Voting;

use ActiveRecordList;
use arException;
use Exception;
use ilObjectTypeMismatchException;
use ilRTE;
use ilUtil;
use LiveVoting\Cache\CachingActiveRecord;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputSubFormGUI;
use LiveVoting\QuestionTypes\NumberRange\xlvoNumberRangeSubFormGUI;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use stdClass;

/**
 * Class xlvoVoting
 *
 * @package LiveVoting\Voting
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoVoting extends CachingActiveRecord {

	const STAT_ACTIVE = 5;
	const STAT_INACTIVE = 1;
	const STAT_INCOMPLETE = 2;
	const ROWS_DEFAULT = 1;
	const TABLE_NAME = 'rep_robj_xlvo_voting_n';


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
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $multi_selection = false;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $colors = false;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $multi_free_input = false;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $obj_id = 0;
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           256
	 */
	protected $title = '';
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           4000
	 */
	protected $description = '';
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        text
	 * @db_length           4000
	 */
	protected $question = '';
	/**
	 * @var string
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $voting_type = xlvoQuestionTypes::TYPE_SINGLE_VOTE;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $voting_status = self::STAT_ACTIVE;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $position = 99;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           2
	 */
	protected $columns = self::ROWS_DEFAULT;
	/**
	 * @var int
	 *
	 * This field must be:
	 * 1 = true
	 * 0 = false
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $percentage = 1;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $start_range = 0;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $end_range = 100;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           8
	 */
	protected $step_range = xlvoNumberRangeSubFormGUI::STEP_RANGE_DEFAULT_VALUE;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $alt_result_display_mode;
	/**
	 * @var bool
	 *
	 * @db_has_field        true
	 * @db_fieldtype        integer
	 * @db_length           1
	 */
	protected $randomise_option_sequence = 0;
	/**
	 * @var xlvoOption[]
	 */
	protected $voting_options = array();
	/**
	 * @var xlvoOption
	 */
	protected $first_voting_option = NULL;
	/**
	 * @var int
	 *
	 * @db_has_field true
	 * @db_fieldtype integer
	 * @db_length    1
	 */
	protected $answer_field = xlvoFreeInputSubFormGUI::ANSWER_FIELD_SINGLE_LINE;


	/**
	 * @param       $primary_key
	 * @param array $add_constructor_args
	 *
	 * @return xlvoVoting
	 */
	public static function findOrGetInstance($primary_key, array $add_constructor_args = array()) {
		return parent::findOrGetInstance($primary_key, $add_constructor_args);
	}


	/**
	 * @return int
	 */
	public function getComputedColums() {
		return (12 / (in_array($this->getColumns(), array(
				1,
				2,
				3,
				4,
			)) ? $this->getColumns() : self::ROWS_DEFAULT));
	}


	/**
	 *
	 */
	public function regenerateOptionSorting() {
		$i = 1;
		foreach ($this->getVotingOptions() as $votingOption) {
			$votingOption->setPosition($i);
			$votingOption->store();
			$i ++;
		}
	}


	/**
	 * @param bool $change_name
	 *
	 * @return xlvoVoting
	 * @throws Exception
	 * @throws arException
	 */
	public function fullClone($change_name = true, $clone_options = true) {
		/**
		 * @var xlvoVoting $newObj
		 * @var xlvoOption $votingOptionNew
		 */
		$newObj = $this->copy();
		if ($change_name) {

			$count = 1;
			while (xlvoVoting::where(array( 'title' => $this->getTitle() . ' (' . $count . ')' ))->where(array( 'obj_id' => $this->getObjId() ))
				->count()) {
				$count ++;
			}

			$newObj->setTitle($this->getTitle() . ' (' . $count . ')');
		}
		$newObj->store();
		if ($clone_options) {
			foreach ($newObj->getVotingOptions() as $votingOption) {
				$votingOptionNew = $votingOption->copy();
				$votingOptionNew->setVotingId($newObj->getId());
				$votingOptionNew->store();
			}
			$newObj->regenerateOptionSorting();
		}

		return $newObj;
	}


	/**
	 *
	 */
	public function create() {
		$res = self::dic()->database()->query('SELECT MAX(position) AS max FROM ' . self::TABLE_NAME . ' WHERE obj_id = ' . self::dic()->database()
				->quote($this->getObjId(), 'integer'));
		$data = self::dic()->database()->fetchObject($res);
		$this->setPosition($data->max + 1);
		parent::create();
	}


	/**
	 * @return ActiveRecordList
	 */
	protected function getFirstLastList($order) {
		return self::where(array( 'obj_id' => $this->getObjId() ))->orderBy('position', $order)
			->where(array( 'voting_type' => xlvoQuestionTypes::getActiveTypes() ));
	}


	/**
	 * @return bool
	 */
	public function isFirst() {
		/**
		 * @var xlvoVoting $first
		 */
		$first = $this->getFirstLastList('ASC')->first();
		if (!$first instanceof self) {
			$first = new self();
		}

		return $first->getId() == $this->getId();
	}


	/**
	 * @return bool
	 */
	public function isLast() {
		/**
		 * @var xlvoVoting $first
		 */
		$first = $this->getFirstLastList('DESC')->first();

		if (!$first instanceof self) {
			$first = new self();
		}

		return $first->getId() == $this->getId();
	}


	/**
	 * @return boolean
	 */
	public function isMultiSelection() {
		return $this->multi_selection;
	}


	/**
	 * @param boolean $multi_selection
	 */
	public function setMultiSelection($multi_selection) {
		$this->multi_selection = $multi_selection;
	}


	/**
	 * @return boolean
	 */
	public function isColors() {
		return $this->colors;
	}


	/**
	 * @param boolean $colors
	 */
	public function setColors($colors) {
		$this->colors = $colors;
	}


	/**
	 * @return boolean
	 */
	public function isMultiFreeInput() {
		return $this->multi_free_input;
	}


	/**
	 * @param boolean $multi_free_input
	 */
	public function setMultiFreeInput($multi_free_input) {
		$this->multi_free_input = $multi_free_input;
	}


	/**
	 * @throws arException
	 */
	public function afterObjectLoad() {
		/**
		 * @var xlvoOption[] $xlvoOptions
		 * @var xlvoOption   $first_voting_option
		 */
		$xlvoOptions = xlvoOption::where(array( 'voting_id' => $this->id ))->orderBy('position')->get();
		$this->setVotingOptions($xlvoOptions);
		$first_voting_option = xlvoOption::where(array( 'voting_id' => $this->id ))->orderBy('position')->first();
		$this->setFirstVotingOption($first_voting_option);
	}


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


	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}


	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return string
	 */
	public function getQuestion() {
		return $this->question;
	}


	/**
	 * @return string
	 */
	public function getQuestionForPresentation() {
		return ilUtil::prepareTextareaOutput($this->getQuestionForEditor(), true);
	}


	/**
	 * @return string
	 */
	public function getRawQuestion() {
		return trim(preg_replace('/\s+/', ' ', strip_tags($this->question)));
	}


	/**
	 * @return string
	 */
	public function getQuestionForEditor() {
		try {
			$prepared = ilRTE::_replaceMediaObjectImageSrc($this->question, 1);
		} catch (ilObjectTypeMismatchException $e) {
			return $this->question;
		}

		return $prepared;
	}


	/**
	 * @param string $question
	 */
	public function setQuestion($question) {
		$this->question = $question;
	}


	/**
	 * @return string
	 */
	public function getVotingType() {
		return $this->voting_type;
	}


	/**
	 * @param string $voting_type
	 */
	public function setVotingType($voting_type) {
		$this->voting_type = $voting_type;
	}


	/**
	 * @return int
	 */
	public function getVotingStatus() {
		return $this->voting_status;
	}


	/**
	 * @param int $voting_status
	 */
	public function setVotingStatus($voting_status) {
		$this->voting_status = $voting_status;
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
	 * @return xlvoOption[]
	 */
	public function getVotingOptions() {
		return $this->voting_options;
	}


	/**
	 * @param xlvoOption[] $voting_options
	 */
	public function setVotingOptions($voting_options) {
		$this->voting_options = $voting_options;
	}


	/**
	 * @return xlvoOption
	 */
	public function getFirstVotingOption() {
		return $this->first_voting_option;
	}


	/**
	 * @param xlvoOption $first_voting_option
	 */
	public function setFirstVotingOption($first_voting_option) {
		$this->first_voting_option = $first_voting_option;
	}


	/**
	 * @return int
	 */
	public function getColumns() {
		return $this->columns;
	}


	/**
	 * @param int $columns
	 */
	public function setColumns($columns) {
		$this->columns = $columns;
	}


	/**
	 * @return int
	 */
	public function getPercentage() {
		return $this->percentage;
	}


	/**
	 * @param int $percentage
	 *
	 * @return xlvoVoting
	 */
	public function setPercentage($percentage) {
		$this->percentage = $percentage;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getStartRange() {
		return $this->start_range;
	}


	/**
	 * @param int $start_range
	 *
	 * @return xlvoVoting
	 */
	public function setStartRange($start_range) {
		$this->start_range = $start_range;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getEndRange() {
		return $this->end_range;
	}


	/**
	 * @param int $end_range
	 *
	 * @return xlvoVoting
	 */
	public function setEndRange($end_range) {
		$this->end_range = $end_range;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getStepRange() {
		return $this->step_range;
	}


	/**
	 * @param int $end_range
	 *
	 * @return xlvoVoting
	 */
	public function setStepRange($step_range) {
		$this->step_range = $step_range;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getAltResultDisplayMode() {
		return $this->alt_result_display_mode;
	}


	/**
	 * @param int $alt_result_display_mode
	 *
	 * @return xlvoVoting
	 */
	public function setAltResultDisplayMode($alt_result_display_mode) {
		$this->alt_result_display_mode = $alt_result_display_mode;

		return $this;
	}


	/**
	 * @return bool
	 */
	public function getRandomiseOptionSequence() {
		return boolval($this->randomise_option_sequence);
	}


	/**
	 * @param bool $randomise_option_sequence
	 *
	 * @return xlvoVoting
	 */
	public function setRandomiseOptionSequence($randomise_option_sequence) {
		$this->randomise_option_sequence = boolval($randomise_option_sequence);

		return $this;
	}


	/**
	 * @return stdClass
	 */
	public function _toJson() {
		$class = new stdClass();
		$class->Id = (int)$this->getId();
		$class->Title = (string)$this->getTitle();
		$class->QuestionType = (string)xlvoQuestionTypes::getClassName($this->getVotingType());
		$class->QuestionTypeId = (int)$this->getVotingType();
		$class->Question = (string)$this->getRawQuestion();
		$class->Position = (int)$this->getPosition();
		foreach ($this->getVotingOptions() as $xlvoOption) {
			$class->Options[] = $xlvoOption->_toJson();
		}

		return $class;
	}


	/**
	 * @return int
	 */
	public function getAnswerField() {
		return $this->answer_field;
	}


	/**
	 * @param int $answer_field
	 */
	public function setAnswerField($answer_field) {
		$this->answer_field = $answer_field;
	}
}
