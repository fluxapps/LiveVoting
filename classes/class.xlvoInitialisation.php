<?php

/**
 * Class xlvoInitialisation
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoInitialisation {

	const CONTEXT_PIN = 1;
	const CONTEXT_ILIAS = 2;
	const XLVO_CONTEXT = 'xlvo_context';
	/**
	 * @var int
	 */
	protected $context = self::CONTEXT_PIN;


	/**
	 * xlvoInitialisation constructor.
	 *
	 * @param int $context
	 */
	protected function __construct($context = NULL) {
		if ($context) {
			$this->context = $context;
			$this->writeToCookie();
		} else {
			$this->readFromCookie();
		}
		$this->initILIAS();
	}


	/**
	 * @param null $context
	 */
	public static function init($context = NULL) {
		new self($context);
	}


	protected function readFromCookie() {
		if (! empty($_COOKIE[self::XLVO_CONTEXT])) {
			self::setContext($_COOKIE[self::XLVO_CONTEXT]);
		} else {
			self::setContext(self::CONTEXT_ILIAS);
		}
	}


	protected function writeToCookie() {
		setcookie(self::XLVO_CONTEXT, $this->getContext(), NULL, '/');
	}


	/**
	 * @return int
	 */
	public function getContext() {
		return $this->context;
	}


	/**
	 * @param int $context
	 */
	public function setContext($context) {
		$this->context = $context;
	}


	protected function initILIAS() {
		chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));

		switch ($this->getContext()) {
			case self::CONTEXT_ILIAS:
				require_once('./include/inc.header.php');

				break;
			case self::CONTEXT_PIN:
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/context/srContext.php");
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/context/srContextLvo.php");
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/context/srInitialisation.php");

				srContext::init('srContextLvo');
				srInitialisation::initILIAS();
				break;
		}
	}
}
