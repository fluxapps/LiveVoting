<?php


namespace LiveVoting\Pin;
use ilLiveVotingPlugin;
use LiveVoting\Cache\xlvoCacheFactory;
use LiveVoting\Cache\xlvoCacheService;
use LiveVoting\Conf\xlvoConf;
use LiveVoting\Exceptions\xlvoVoterException;
use LiveVoting\User\xlvoUser;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Voting\xlvoVotingConfig;
use srag\DIC\LiveVoting\DICTrait;
use stdClass;
use \ilObjLiveVotingAccess;
use LiveVoting\Context\Param\ParamManager;

/**
 * Class xlvoPin
 *
 * @package LiveVoting\Pin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoPin {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	/**
	 * @var string
	 */
	protected $pin = '';
	/**
	 * @var bool
	 */
	protected $use_lowercase = false;
	/**
	 * @var bool
	 */
	protected $use_uppercase = true;
	/**
	 * @var bool
	 */
	protected $use_numbers = true;
	/**
	 * @var int
	 */
	protected $pin_length = 4;
	/**
	 * @var xlvoCacheService $cache
	 */
	private $cache;
	const CACHE_TTL_SECONDS = 1800;


	/**
	 * @param string $pin
	 * @param bool   $force_not_format
	 *
	 * @return string
	 */
	public static function formatPin($pin, $force_not_format = false) {
		if (!$force_not_format && xlvoConf::getConfig(xlvoConf::F_USE_SERIF_FONT_FOR_PINS)) {
			$pin = '<span class="serif_font">' . $pin . '</span>';
		}

		return $pin;
	}


	/**
	 * @param int $obj_id
	 *
	 * @return int
	 */
	public static function lookupPin($obj_id) {
		/**
		 * @var xlvoVotingConfig $xlvoVotingConfig
		 */
		$xlvoVotingConfig = xlvoVotingConfig::findOrGetInstance($obj_id);

		return $xlvoVotingConfig->getPin();
	}


	/**
	 * @param string $pin
	 *
	 * @return int
	 * @throws xlvoVoterException
	 */
	public static function checkPinAndGetObjId($pin, $safe_mode = true) {
		$cache = xlvoCacheFactory::getInstance();

		if ($cache->isActive()) {
			return self::checkPinAndGetObjIdWithCache($pin, $safe_mode);
		} else {
			return self::checkPinAndGetObjIdWithoutCache($pin, $safe_mode);
		}
	}


	/**
	 * @param string $pin
	 * @param bool   $safe_mode
	 *
	 * @return int
	 * @throws xlvoVoterException
	 */
	private static function checkPinAndGetObjIdWithCache($pin, $safe_mode = true) {
		global $DIC;

		//use cache to speed up pin fetch operation
		$key = xlvoVotingConfig::TABLE_NAME . '_pin_' . $pin;
		$cache = xlvoCacheFactory::getInstance();

		$config = $cache->get($key);
		$xlvoVotingConfig = NULL;

		if (!$config instanceof stdClass) {
			//save obj id for a later cache fetch
			//if we store the object a second time we would have some consistency problems because we don't know when the data are updated.
			$xlvoVotingConfig = xlvoVotingConfig::where(array( 'pin' => $pin ))->first();
			$config = new stdClass();

			//if the object is not gone
			if ($xlvoVotingConfig instanceof xlvoVotingConfig) {
				$config->id = $xlvoVotingConfig->getPrimaryFieldValue();
				$cache->set($key, $config, self::CACHE_TTL_SECONDS);
			}
		}

		if (!($xlvoVotingConfig instanceof xlvoVotingConfig)) {
			$xlvoVotingConfig = xlvoVotingConfig::find($config->id); //relay on ar connector cache
		}

		$param_manager = ParamManager::getInstance();

		//check pin
		if ($xlvoVotingConfig instanceof xlvoVotingConfig) {
			if (!$xlvoVotingConfig->isObjOnline() && !ilObjLiveVotingAccess::hasWriteAccess($param_manager->getRefId(),$DIC->user()->getId())) {
				if ($safe_mode) {
					throw new xlvoVoterException('The voting is currently offline.', xlvoVoterException::VOTING_OFFLINE);
				}
			}
			if (!$xlvoVotingConfig->isAnonymous() && xlvoUser::getInstance()->isPINUser()) {
				if ($safe_mode) {
					throw new xlvoVoterException('The voting is not available for anonymous users.', xlvoVoterException::VOTING_NOT_ANONYMOUS);
				}
			}

			if (!$xlvoVotingConfig->isAvailableForUser() && xlvoUser::getInstance()->isPINUser()) {
				if ($safe_mode) {
					throw new xlvoVoterException('The voting is currently unavailable.', xlvoVoterException::VOTING_UNAVAILABLE);
				}
			}

			return $xlvoVotingConfig->getObjId();
		}
		if ($safe_mode) {
			throw new xlvoVoterException('', xlvoVoterException::VOTING_PIN_NOT_FOUND);
		}

		return 0;
	}


	/**
	 * @param string $pin
	 * @param bool   $safe_mode
	 *
	 * @return int
	 * @throws xlvoVoterException
	 */
	private static function checkPinAndGetObjIdWithoutCache($pin, $safe_mode = true) {
		$xlvoVotingConfig = xlvoVotingConfig::where(array( 'pin' => $pin ))->first();

		//check pin
		if ($xlvoVotingConfig instanceof xlvoVotingConfig) {
			if (!$xlvoVotingConfig->isObjOnline()) {
				if ($safe_mode) {
					throw new xlvoVoterException('', xlvoVoterException::VOTING_OFFLINE);
				}
			}
			if (!$xlvoVotingConfig->isAnonymous() && xlvoUser::getInstance()->isPINUser()) {
				if ($safe_mode) {
					throw new xlvoVoterException('', xlvoVoterException::VOTING_NOT_ANONYMOUS);
				}
			}

			if (!$xlvoVotingConfig->isAvailableForUser() && xlvoUser::getInstance()->isPINUser()) {
				if ($safe_mode) {
					throw new xlvoVoterException('', xlvoVoterException::VOTING_UNAVAILABLE);
				}
			}

			return $xlvoVotingConfig->getObjId();
		}
		if ($safe_mode) {
			throw new xlvoVoterException('', xlvoVoterException::VOTING_PIN_NOT_FOUND);
		}

		return 0;
	}


	/**
	 * xlvoPin constructor.
	 *
	 * @param string $pin
	 */
	public function __construct($pin = '') {
		if (!$pin) {
			$this->generatePIN();
		} else {
			$this->setPin($pin);
		}

		$this->cache = xlvoCacheFactory::getInstance();
	}


	/**
	 *
	 */
	protected function generatePIN() {
		$array = array();

		// numbers
		if ($this->isUseNumbers()) {
			for ($i = 48; $i < 58; $i ++) {
				$array[] = chr($i);
			}
		}

		// lower case
		if ($this->isUseLowercase()) {
			for ($i = 97; $i <= 122; $i ++) {
				$array[] = chr($i);
			}
		}

		// upper case
		if ($this->isUseUppercase()) {
			for ($i = 65; $i <= 90; $i ++) {
				$array[] = chr($i);
			}
		}

		$pin = '';
		$pin_found = false;

		while (!$pin_found) {
			for ($i = 1; $i <= $this->getPinLength(); $i ++) {
				$rnd = mt_rand(0, count($array) - 1);
				$pin .= $array[$rnd];
			}
			if (xlvoVotingConfig::where(array( 'pin' => $pin ))->count() <= 0) {
				$pin_found = true;
			}
		}

		$this->setPin($pin);
	}


	/**
	 * @return bool|string
	 */
	public function getLastAccess() {
		if ($this->cache->isActive()) {
			return $this->getLastAccessWithCache();
		} else {
			return $this->getLastAccessWithoutCache();
		}
	}


	/**
	 * @return bool|string
	 */
	private function getLastAccessWithCache() {
		$key = xlvoVotingConfig::TABLE_NAME . '_pin_' . $this->getPin();
		/**
		 * @var stdClass $xlvoVotingConfig
		 */
		$xlvoVotingConfig = $this->cache->get($key);

		if (!($xlvoVotingConfig instanceof stdClass)) {
			$xlvoVotingConfig = xlvoVotingConfig::where(array( 'pin' => $this->getPin() ))->first();
			$config = new stdClass();

			//if the object is not gone
			if ($xlvoVotingConfig instanceof xlvoVotingConfig) {
				$config->id = $xlvoVotingConfig->getPrimaryFieldValue();
				$this->cache->set($key, $config, self::CACHE_TTL_SECONDS);

				return $xlvoVotingConfig->getLastAccess();
			}

			if (!($xlvoVotingConfig instanceof xlvoVotingConfig)) {
				return false;
			}
		}

		/**
		 * @var xlvoVotingConfig $xlvoVotingConfigObject
		 */
		$xlvoVotingConfigObject = xlvoVotingConfig::find($xlvoVotingConfig->id);

		return $xlvoVotingConfigObject->getLastAccess();
	}


	/**
	 * @return bool|string
	 */
	private function getLastAccessWithoutCache() {
		$xlvoVotingConfig = xlvoVotingConfig::where(array( 'pin' => $this->getPin() ))->first();

		if (!($xlvoVotingConfig instanceof xlvoVotingConfig)) {
			return false;
		}

		return $xlvoVotingConfig->getLastAccess();
	}


	/**
	 * @return string
	 */
	public function getPin() {
		return $this->pin;
	}


	/**
	 * @param string $pin
	 */
	public function setPin($pin) {
		$this->pin = $pin;
	}


	/**
	 * @return boolean
	 */
	public function isUseLowercase() {
		return $this->use_lowercase;
	}


	/**
	 * @param boolean $use_lowercase
	 */
	public function setUseLowercase($use_lowercase) {
		$this->use_lowercase = $use_lowercase;
	}


	/**
	 * @return boolean
	 */
	public function isUseUppercase() {
		return $this->use_uppercase;
	}


	/**
	 * @param boolean $use_uppercase
	 */
	public function setUseUppercase($use_uppercase) {
		$this->use_uppercase = $use_uppercase;
	}


	/**
	 * @return boolean
	 */
	public function isUseNumbers() {
		return $this->use_numbers;
	}


	/**
	 * @param boolean $use_numbers
	 */
	public function setUseNumbers($use_numbers) {
		$this->use_numbers = $use_numbers;
	}


	/**
	 * @return int
	 */
	public function getPinLength() {
		return $this->pin_length;
	}


	/**
	 * @param int $pin_length
	 */
	public function setPinLength($pin_length) {
		$this->pin_length = $pin_length;
	}
}
