<?php

namespace LiveVoting\QuestionTypes;

use ilLiveVotingPlugin;
use LiveVoting\Exceptions\xlvoVotingManagerException;
use LiveVoting\Utils\LiveVotingTrait;
use ReflectionClass;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoQuestionTypes
 *
 * @package LiveVoting\QuestionTypes
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoQuestionTypes {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	const TYPE_SINGLE_VOTE = 1;
	const TYPE_FREE_INPUT = 2;
	const TYPE_RANGE = 3;
	const TYPE_CORRECT_ORDER = 4;
	const TYPE_FREE_ORDER = 5;
	const TYPE_NUMBER_RANGE = 6;
	const SINGLE_VOTE = 'SingleVote';
	const FREE_INPUT = 'FreeInput';
	const CORRECT_ORDER = 'CorrectOrder';
	const FREE_ORDER = 'FreeOrder';
	const NUMBER_RANGE = 'NumberRange';
	/**
	 * @var array
	 */
	protected static $active_types = array(
		self::TYPE_FREE_INPUT,
		self::TYPE_SINGLE_VOTE,
		self::TYPE_CORRECT_ORDER,
		self::TYPE_FREE_ORDER,
		self::TYPE_NUMBER_RANGE
	);
	/**
	 * @var array
	 */
	protected static $class_map = array(
		self::TYPE_SINGLE_VOTE => self::SINGLE_VOTE,
		self::TYPE_FREE_INPUT => self::FREE_INPUT,
		self::TYPE_CORRECT_ORDER => self::CORRECT_ORDER,
		self::TYPE_FREE_ORDER => self::FREE_ORDER,
		self::TYPE_NUMBER_RANGE => self::NUMBER_RANGE
	);


	/**
	 * @return array
	 */
	public static function getActiveTypes() {
		// TODO: Just return self::$active_types;

		$f = new ReflectionClass(self::class);
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
	 *
	 * @return mixed
	 * @throws xlvoVotingManagerException
	 */
	public static function getClassName($type) {
		if (!isset(self::$class_map[$type])) {
			//			throw new xlvoVotingManagerException('Type not available');
		}

		return self::$class_map[$type];
	}
}
