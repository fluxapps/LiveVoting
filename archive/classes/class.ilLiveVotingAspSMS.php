<?php
require_once('class.ilObjLiveVotingGUI.php');
require_once('class.ilObjLiveVoting.php');
require_once('class.ilLiveVotingSMS.php');
require_once('class.ilLiveVotingConfigGUI.php');

class ilLiveVotingAspSMS extends ilLiveVotingSMS {

	/**
	 * @param array $data
	 */
	public function __construct(array $data) {
		if (!ilLiveVotingConfigGUI::_getValue('aspsms')) {
			exit;
		}
		$this->setOriginator(ilLiveVotingConfigGUI::_getValue('aspsms_originator'));
		$this->setSendsmsError(ilLiveVotingConfigGUI::_getValue('aspsms_sendsms_error'));
		$this->setSendsmsOk(ilLiveVotingConfigGUI::_getValue('aspsms_sendsms_ok'));
		$this->setSendsmsFlash(ilLiveVotingConfigGUI::_getValue('aspsms_sendsms_flash'));
		$this->setUserkey(ilLiveVotingConfigGUI::_getValue('aspsms_userkey'));
		$this->setPassword(ilLiveVotingConfigGUI::_getValue('aspsms_password'));
		$this->setRecipient($data['ORIG']);
		$this->setRequestUrl('http://xml2.aspsms.com/xmlsvr.asp');
		$this->setPort(5061);

		if ($c = preg_match_all("/" . '(\\d+).*?((?:[a-z][a-z0-9_]*))' . "/is", $data['MSG'], $matches)) {
			$this->setPin($matches[1][0]);
			$this->setVote($matches[2][0]);
		}
		parent::__construct();
	}


	/**
	 * Send SMS
	 */
	public function send() {
		$xml = new SimpleXMLElement("<aspsms></aspsms>");
		$xml->addAttribute('encoding', 'UTF-8');
		$xml->addChild('Userkey', $this->getUserkey());
		$xml->addChild('AffiliateId', '96143');
		$xml->addChild('Password', $this->getPassword());
		$xml->addChild('Originator', $this->getOriginator());
		$recipient = $xml->addChild('Recipient');
		$recipient->addChild("PhoneNumber", $this->getRecipient());
		//$recipient->addChild("TransRefNumber", $this->getPin() . time());
		$xml->addChild('MessageData', $this->getMessage());
		if ($this->getSendsmsFlash()) {
			$xml->addChild('FlashingSMS', 1);
		}
		$xml->addChild('Action', 'SendTextSMS');

		// Send XML
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl());
		curl_setopt($ch, CURLOPT_HTTP_VERSION, "1.0");
		curl_setopt($ch, CURLOPT_PORT, $this->getPort());
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());

		$result = curl_exec($ch);
		//$header = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		$body = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

		$return = new SimpleXMLElement($body);
		if ($return->ErrorCode != "1") {
			$this->log->write($return->ErrorDescription);
		}
		exit;
	}
}

?>