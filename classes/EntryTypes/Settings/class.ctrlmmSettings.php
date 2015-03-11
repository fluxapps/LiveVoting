<?php

/**
 * Class ctrlmmSettings
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 2.0.02
 */
class ctrlmmSettings {

	const SALT = 'fde21db8bc55aabcb0ba4e5e7ef02ajU';
	const INCOMING_TYPE = 'incoming_type';
	const LANGUAGE = 'language';
	const RESULTS = 'results';
	const SKIN = 'skin';
	const USR_TOKEN = 'usr_token';
	/**
	 * @var int
	 */
	protected $usr_id;
	/**
	 * @var int
	 */
	protected $incoming_type;
	/**
	 * @var string
	 */
	protected $language;
	/**
	 * @var int
	 */
	protected $results;
	/**
	 * @var string
	 */
	protected $skin;
	/**
	 * @var string
	 */
	protected $usr_token;
	/**
	 * @var bool
	 */
	protected $response = false;


	/**
	 * @param array $post
	 *
	 * @internal param $gui_classes
	 */
	public static function save(array $post) {
		new self($post);
	}


	/**
	 * @param array $post
	 *
	 * @internal param $gui_classes
	 */
	protected function __construct(array $post) {
		self::initILIAS();
		$this->setIncomingType((int)$post[self::INCOMING_TYPE]);
		$this->setLanguage($post[self::LANGUAGE]);
		$this->setResults($post[self::RESULTS]);
		$this->setSkin($post[self::SKIN]);
		$this->setUsrToken($post[self::USR_TOKEN]);
		$this->setUsrId(self::dec($post[self::USR_TOKEN]));
		$this->saveData();
		$this->printJson();
	}


	protected function saveData() {
		$ilUser = new ilObjUser($this->getUsrId());
		$ilUser->setPref("hits_per_page", $this->getResults());
		$sknst = explode(":", $this->getSkin());
		$ilUser->setPref("skin", $sknst[0]);
		$ilUser->setPref("style", $sknst[1]);
		$ilUser->setLanguage($this->getLanguage());
		$ilUser->update();

		$mailOptions = new ilMailOptions($this->getUsrId());
		$signature = $mailOptions->getSignature();
		$linebreak = $mailOptions->getLinebreak();
		$cronjob_notification = $mailOptions->getCronjobNotification();
		$mailOptions->updateOptions($signature, $linebreak, $this->getIncomingType(), $cronjob_notification);

		$this->setResponse(true);
	}


	protected function printJson() {
		header('Content-Type: application/json');
		echo json_encode($this->getResponse());
	}


	/**
	 * @param int $incoming_type
	 */
	public function setIncomingType($incoming_type) {
		$this->incoming_type = $incoming_type;
	}


	/**
	 * @return int
	 */
	public function getIncomingType() {
		return $this->incoming_type;
	}


	/**
	 * @param string $language
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}


	/**
	 * @return string
	 */
	public function getLanguage() {
		return $this->language;
	}


	/**
	 * @param int $results
	 */
	public function setResults($results) {
		$this->results = $results;
	}


	/**
	 * @return int
	 */
	public function getResults() {
		return $this->results;
	}


	/**
	 * @param string $skin
	 */
	public function setSkin($skin) {
		$this->skin = $skin;
	}


	/**
	 * @return string
	 */
	public function getSkin() {
		return $this->skin;
	}


	/**
	 * @param int $usr_id
	 */
	public function setUsrId($usr_id) {
		$this->usr_id = $usr_id;
	}


	/**
	 * @return int
	 */
	public function getUsrId() {
		return $this->usr_id;
	}


	/**
	 * @param string $usr_token
	 */
	public function setUsrToken($usr_token) {
		$this->usr_token = $usr_token;
	}


	/**
	 * @return string
	 */
	public function getUsrToken() {
		return $this->usr_token;
	}


	/**
	 * @param boolean $response
	 */
	public function setResponse($response) {
		$this->response = $response;
	}


	/**
	 * @return boolean
	 */
	public function getResponse() {
		return $this->response;
	}

	//
	// Helpers
	//
	protected static function initILIAS() {
		$path = stristr(__FILE__, 'Customizing', true);
		chdir($path);
		require_once('include/inc.header.php');
		self::includes();
	}


	protected static function includes() {
		require_once('Services/Mail/classes/class.ilMailOptions.php');
	}


	/**
	 * @param $text
	 *
	 * @return string
	 */
	public static function enc($text) {
		if (self::isMcryptInstalled()) {
			return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
		} else {
			return $text;
		}
	}


	/**
	 * @param $text
	 *
	 * @return string
	 */
	public static function dec($text) {
		if (self::isMcryptInstalled()) {
			return trim(@mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
		} else {
			return $text;
		}
	}


	/**
	 * @return bool
	 */
	public static function isMcryptInstalled() {
		return function_exists('mcrypt_encrypt');
	}
}

?>