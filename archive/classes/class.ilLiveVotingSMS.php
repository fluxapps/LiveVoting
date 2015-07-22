<?php

require_once("./Services/Logging/classes/class.ilLog.php");
require_once('class.ilLiveVotingPlugin.php');
require_once('class.ilObjLiveVotingGUI.php');
require_once('class.ilObjLiveVoting.php');
require_once('class.ilLiveVotingConfigGUI.php');

class ilLiveVotingSMS {

	const SMS_ERROR_ANONYMOUS = 1;
	const SMS_ERROR_WRONGPIN = 2;
	const SMS_ERROR_VOTEOVER = 3;
	const SMS_ERROR_NOSUCHVOTE = 4;
	/**
	 * @var
	 */
	protected $object;
	/**
	 * @var
	 */
	protected $pin;
	/**
	 * @var
	 */
	protected $originator;
	/**
	 * @var
	 */
	protected $log;
	/**
	 * @var
	 */
	protected $error;
	/**
	 * @var
	 */
	protected $request_url;
	/**
	 * @var
	 */
	protected $xml;
	/**
	 * @var
	 */
	protected $userkey;
	/**
	 * @var
	 */
	protected $password;
	/**
	 * @var
	 */
	protected $sendsms_ok;
	/**
	 * @var
	 */
	protected $sendsms_error;
	/**
	 * @var
	 */
	protected $sendsms_flash;
	/**
	 * @var
	 */
	protected $vote;
	/**
	 * @var
	 */
	protected $message;
	/**
	 * @var
	 */
	protected $recipient;
	/**
	 * @var
	 */
	protected $port;


	public function __construct() {
		if (ilLiveVotingConfigGUI::_getValue('use_smslog')) {
			$this->log = new ilLog(dirname(__FILE__) . "/..", "sms.log", "SMS:" . time(), true);
		} else {
			$this->log = new DummyLog();
		}

		$this->lng = ilLiveVotingPlugin::getInstance();
		$this->log->write("SMS Voting initiated");
		$this->object = ilObjLiveVoting::_getObjectByPin($this->getPin());

		if (!$this->object) {
			$this->setError(self::SMS_ERROR_WRONGPIN);
		}

		if (!ilObjLiveVoting::_isGlobalAnonymForPin($this->getPin())) {
			$this->setError(self::SMS_ERROR_ANONYMOUS);
		}

		$this->vote();
	}


	/**
	 * Send SMS
	 */
	public function send() {
		// Defined in SubClasses
	}


	/**
	 * Vote
	 */
	public function vote() {
		$options = $this->object->getOptions();

		// GOEA
		if (count($options) == 3 AND md5(strtolower($this->getVote())) == "fea0543b0b4b9d4c240c6219de0db41c") {
			foreach ($options as $option) {
				$ids[] = $option->getId();
			}
			for ($i = 0; $i < 30; $i ++) {
				$this->object->vote($ids[0], 0, rand(0, 100000000));
			}
			for ($i = 0; $i < 15; $i ++) {
				$this->object->vote($ids[1], 0, rand(0, 100000000));
				$this->object->vote($ids[2], 0, rand(0, 100000000));
			}
			$this->log->write("Life's a game!! Thanks to " . $this->getRecipient() . " for PIN " . $this->getPin());
			$this->setMessage("Life's a game");
			$this->send();
		}

		// VOTING
		$i = 0;
		$id = false;
		foreach ($options as $option) {
			if (chr(65 + $i) == strtoupper($this->getVote())) {
				$id = $option->getId();
			}
			$i ++;
		}
		if ($id) {
			if (!$this->object->vote($id, 0, $this->getRecipient())) {
				$this->setError(self::SMS_ERROR_VOTEOVER);

				return false;
			} elseif ($this->getSendsmsOk()) {
				$this->log->write("Vote " . $this->getVote() . " for PIN " . $this->getPin() . " from " . $this->getRecipient());
				$this->setMessage($this->lng->txt('sms_vote_ok'));
				$this->send();

				return true;
			} else {
				$this->log->write("Vote " . $this->getVote() . " for PIN " . $this->getPin() . " from " . $this->getRecipient());
			}
		} else {
			$this->setError(self::SMS_ERROR_NOSUCHVOTE);

			return false;
		}
	}


	/**
	 * @param $error
	 */
	public function setError($error) {
		$this->error = $error;
		$this->log->write("ERROR " . $error . " for PIN " . $this->getPin() . " from " . $this->getRecipient());
		$this->setMessage($this->getErrorString());

		if ($this->getSendsmsError()) {
			$this->send();
		} else {
			exit;
		}
	}


	/**
	 * @return mixed
	 */
	public function getError() {
		return $this->error;
	}


	/**
	 * @param $originator
	 */
	public function setOriginator($originator) {
		$this->originator = $originator;
	}


	/**
	 * @return mixed
	 */
	public function getOriginator() {
		return $this->originator;
	}


	/**
	 * @param $pin
	 */
	public function setPin($pin) {
		$this->pin = $pin;
	}


	/**
	 * @return mixed
	 */
	public function getPin() {
		return $this->pin;
	}


	/**
	 * @param $vote
	 */
	public function setVote($vote) {
		$this->vote = $vote;
	}


	/**
	 * @return mixed
	 */
	public function getVote() {
		return $this->vote;
	}


	/**
	 * @param $request_url
	 */
	public function setRequestUrl($request_url) {
		$this->request_url = $request_url;
	}


	/**
	 * @return mixed
	 */
	public function getRequestUrl() {
		return $this->request_url;
	}


	/**
	 * @param $xml
	 */
	public function setXml($xml) {
		$this->xml = $xml;
	}


	/**
	 * @return mixed
	 */
	public function getXml() {
		return $this->xml;
	}


	/**
	 * @param $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}


	/**
	 * @return mixed
	 */
	public function getPassword() {
		return $this->password;
	}


	/**
	 * @param $sendsms_error
	 */
	public function setSendsmsError($sendsms_error) {
		$this->sendsms_error = $sendsms_error;
	}


	/**
	 * @return mixed
	 */
	public function getSendsmsError() {
		return $this->sendsms_error;
	}


	/**
	 * @param $sendsms_flash
	 */
	public function setSendsmsFlash($sendsms_flash) {
		$this->sendsms_flash = $sendsms_flash;
	}


	/**
	 * @return mixed
	 */
	public function getSendsmsFlash() {
		return $this->sendsms_flash;
	}


	/**
	 * @param $sendsms_ok
	 */
	public function setSendsmsOk($sendsms_ok) {
		$this->sendsms_ok = $sendsms_ok;
	}


	/**
	 * @return mixed
	 */
	public function getSendsmsOk() {
		return $this->sendsms_ok;
	}


	/**
	 * @param $userkey
	 */
	public function setUserkey($userkey) {
		$this->userkey = $userkey;
	}


	/**
	 * @return mixed
	 */
	public function getUserkey() {
		return $this->userkey;
	}


	public function getErrorString() {
		$errorCodes = array(
			self::SMS_ERROR_ANONYMOUS => $this->lng->txt('sms_err_notanonimous'),
			self::SMS_ERROR_WRONGPIN => $this->lng->txt('sms_err_wrongpin'),
			self::SMS_ERROR_VOTEOVER => $this->lng->txt('sms_err_voteover'),
			self::SMS_ERROR_NOSUCHVOTE => $this->lng->txt('sms_err_nosuchvote'),
		);

		return $errorCodes[$this->getError()];
	}


	/**
	 * @param  $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}


	/**
	 * @return
	 */
	public function getMessage() {
		return $this->message;
	}


	/**
	 * @param  $recipient
	 */
	public function setRecipient($recipient) {
		$this->recipient = sha1($recipient);
	}


	/**
	 * @return
	 */
	public function getRecipient() {
		return $this->recipient;
	}


	/**
	 * @param  $port
	 */
	public function setPort($port) {
		$this->port = $port;
	}


	/**
	 * @return
	 */
	public function getPort() {
		return $this->port;
	}
}

class DummyLog {

	function __construct() {
	}


	function write($dummy) {
	}
}

?>