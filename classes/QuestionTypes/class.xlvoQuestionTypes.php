<?php

namespace LiveVoting\QuestionTypes;
use LiveVoting\Exceptions\xlvoVotingManagerException;

/**
 * Class xlvoQuestionTypes
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoQuestionTypes {

	const TYPE_SINGLE_VOTE = 1;
	const TYPE_FREE_INPUT = 2;
	const TYPE_RANGE = 3;
	const TYPE_CORRECT_ORDER = 4;
	const TYPE_FREE_ORDER = 5;
	/**
	 * @var array
	 */
	protected static $active_types = array(
		self::TYPE_FREE_INPUT,
		self::TYPE_SINGLE_VOTE,
		self::TYPE_CORRECT_ORDER,
		self::TYPE_FREE_ORDER,
	);
	/**
	 * @var array
	 */
	protected static $class_map = array(
		self::TYPE_SINGLE_VOTE   => 'SingleVote',
		self::TYPE_FREE_INPUT    => 'FreeInput',
		self::TYPE_CORRECT_ORDER => 'CorrectOrder',
		self::TYPE_FREE_ORDER    => 'FreeOrder',
	);


	/**
	 * @return array
	 */
	public static function getActiveTypes() {
		$f = new \ReflectionClass('LiveVoting\QuestionTypes\xlvoQuestionTypes');
		$types = array();
		foreach ($f->getConstants() as $constant_name => $constant) {
			if (strpos($constant_name, 'TYPE_') === 0 && in_array($constant, self::$active_types)) {
				$types[] = $constant;
			}
		}

		return $types;
	}


	/**
	 * @param $type
	 * @return mixed
	 * @throws xlvoVotingManagerException
	 */
	public static function getClassName($type) {
		if (!isset(self::$class_map[$type])) {
			//			throw  new xlvoVotingManagerException('Type not available');
		}

		return self::$class_map[$type];
	}
}