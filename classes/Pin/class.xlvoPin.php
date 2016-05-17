<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingConfig.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/User/class.xlvoUser.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voter/ex.xlvoVoterException.php');

/**
 * Class xlvoPin
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoPin {

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
	protected $use_uppercase = false;
	/**
	 * @var bool
	 */
	protected $use_numbers = true;
	/**
	 * @var int
	 */
	protected $pin_length = 4;


	/**
	 * @param $obj_id
	 * @return int
	 */
	public static function lookupPin($obj_id) {
		/**
		 * @var $xlvoVotingConfig xlvoVotingConfig
		 */
		$xlvoVotingConfig = xlvoVotingConfig::findOrGetInstance($obj_id);

		return $xlvoVotingConfig->getPin();
	}


	/**
	 * @param $pin
	 * @return int
	 * @throws \xlvoVoterException
	 */
	public static function checkPin($pin, $safe_mode = true) {
		$xlvoVotingConfig = xlvoVotingConfig::where(array( 'pin' => $pin ))->first();
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
	}


	/**
	 * xlvoPin constructor.
	 */
	public function __construct($pin = '') {
		if (!$pin) {
			$this->generatePIN();
		} else {
			$this->setPin($pin);
		}
	}


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
		$xlvoVotingConfig = xlvoVotingConfig::where(array( 'pin' => $this->getPin() ))->first();
		if ($xlvoVotingConfig instanceof xlvoVotingConfig) {
			return $xlvoVotingConfig->getLastAccess();
		} else {
			return false;
		}
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
