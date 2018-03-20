<?php

namespace LiveVoting\Voter;

use LiveVoting\Cache\CachingActiveRecord;
use LiveVoting\Conf\xlvoConf;
use LiveVoting\User\xlvoUser;

/**
 * Class xlvoVoter
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoVoter extends CachingActiveRecord  {

    /**
     * Default client update delay in seconds
     */
    const DEFAULT_CLIENT_UPDATE_DELAY = 1;
    const TABLE_NAME = 'xlvo_voter';

	/**
	 * @return string
	 * @description Return the Name of your Database Table
	 * @deprecated
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}

	/**
	 * @param $player_id
	 */
	public static function register($player_id) {
		$obj = xlvoVoter::where(array(
			'user_identifier' => xlvoUser::getInstance()->getIdentifier(),
			'player_id' => $player_id
		))->first();

		if (!$obj instanceof xlvoVoter) {
			$obj = new self();
			$obj->setUserIdentifier(xlvoUser::getInstance()->getIdentifier());
			$obj->setPlayerId($player_id);
			$obj->create();
		}
		$obj->setLastAccess(new \DateTime());
		$obj->update();
	}


	/**
	 * @param $player_id
	 * @return int
	 */
	public static function countVoters($player_id) {
        /**
         * @var $delay float
         */
        $delay = xlvoConf::getConfig(xlvoConf::F_REQUEST_FREQUENCY);

        //check if we get some valid settings otherwise fall back to default value.
        if(is_numeric($delay))
        {
            $delay = ((float)$delay);
        }
        else
        {
            $delay = self::DEFAULT_CLIENT_UPDATE_DELAY;
        }
		return self::where(array( 'player_id' => $player_id ))->where(array( 'last_access' => date(DATE_ATOM, time() - ($delay + $delay * 0.5)) ), '>')->count();
	}


	/**
	 * @param $field_name
	 *
	 * @return mixed
	 */
	public function sleep($field_name) {
		if ($field_name == 'last_access') {
			if (!$this->last_access instanceof \DateTime) {
				$this->last_access = new \DateTime();
			}
			return $this->last_access->format(\DateTime::ATOM);
		}
		return null;
	}


	/**
	 * @param $field_name
	 * @param $field_value
	 *
	 * @return mixed
	 */
	public function wakeUp($field_name, $field_value) {
		if ($field_name == 'last_access') {
			return new \DateTime($field_value);
		}
		return null;
	}


	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_sequence  true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $id;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $player_id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     128
	 */
	protected $user_identifier;
	/**
	 * @var \DateTime
	 *
	 * @con_has_field  true
	 * @con_fieldtype  timestamp
	 */
	protected $last_access;


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
	public function getPlayerId() {
		return $this->player_id;
	}


	/**
	 * @param int $player_id
	 */
	public function setPlayerId($player_id) {
		$this->player_id = $player_id;
	}


	/**
	 * @return string
	 */
	public function getUserIdentifier() {
		return $this->user_identifier;
	}


	/**
	 * @param string $user_identifier
	 */
	public function setUserIdentifier($user_identifier) {
		$this->user_identifier = $user_identifier;
	}


	/**
	 * @return \DateTime
	 */
	public function getLastAccess() {
		return $this->last_access;
	}


	/**
	 * @param \DateTime $last_access
	 */
	public function setLastAccess($last_access) {
		$this->last_access = $last_access;
	}
}
