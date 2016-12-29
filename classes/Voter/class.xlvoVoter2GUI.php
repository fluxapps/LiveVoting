<?php

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Context\xlvoInitialisation;
use LiveVoting\Js\xlvoJs;
use LiveVoting\Js\xlvoJsResponse;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Player\xlvoPlayer;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\Voter\xlvoVoter;
use LiveVoting\Voter\xlvoVoterException;
use LiveVoting\Voting\xlvoVotingManager2;

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');

/**
 * Class xlvoVoter2GUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xlvoVoter2GUI: ilUIPluginRouterGUI
 */
class xlvoVoter2GUI extends xlvoGUI {

	const CMD_CHECK_PIN = 'checkPin';
	const F_PIN_INPUT = 'pin_input';
	const CMD_START_VOTER_PLAYER = 'startVoterPlayer';
	const CMD_GET_VOTING_DATA = 'loadVotingData';
	const DEBUG = false;
	/**
	 * @var string
	 */
	protected $pin = '';
	/**
	 * @var xlvoVotingManager2
	 */
	protected $manager;


	/**
	 * @param $key
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('voter_' . $key);
	}


	public function executeCommand() {
		global $ilUser;
		$this->pin = xlvoInitialisation::getCookiePIN();
		$this->manager = new xlvoVotingManager2($this->pin, true);
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			case '':
				if (!$this->manager->getVotingConfig()->isAnonymous() && (is_null($ilUser) || $ilUser->getId() == 13 || $ilUser->getId() == 0)) {
					//remove plugin path to get "real" web root otherwise we break installations with context paths -> http://demo.ilias.ch/test/goto.php
					$plugin_path = "Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting";
					$ilias_base_path = str_replace($plugin_path, '', ILIAS_HTTP_PATH);
					$login_target = "{$ilias_base_path}goto.php?target=xlvo_1_pin_" . $this->pin;

					//redirect
					$this->tpl->setContent("<script>window.location.replace('$login_target');</script>");
					$this->tpl->show("content");
				} else {
					parent::executeCommand();
				}

				break;
			default:
				// Question-types
				require_once($this->ctrl->lookupClassPath($nextClass));
				$gui = new $nextClass();
				if ($gui instanceof xlvoQuestionTypesGUI) {
					$gui->setManager($this->manager);
				}
				$this->ctrl->forwardCommand($gui);
				break;
		}
	}


	protected function index() {
		if ($this->manager->getObjId() > 0) {
			$this->ctrl->redirect($this, self::CMD_START_VOTER_PLAYER);
		}

		$tpl = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voter/tpl.pin.html', true, false);
		$this->tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voter/pin.css');
		$pin_form = new \ilPropertyFormGUI();
		$pin_form->setFormAction($this->ctrl->getLinkTarget($this, self::CMD_CHECK_PIN));
		$pin_form->addCommandButton(self::CMD_CHECK_PIN, $this->txt('send'));

		$xlvoPin = new xlvoPin();

		$te = new \ilTextInputGUI($this->txt(self::F_PIN_INPUT), self::F_PIN_INPUT);
		$te->setMaxLength($xlvoPin->getPinLength());
		$pin_form->addItem($te);

		$tpl->setVariable('TITLE', $this->txt('pin_form_title'));
		$tpl->setVariable('FORM', $pin_form->getHTML());

		$this->tpl->setContent($tpl->get());
	}


	protected function checkPin() {
		$redirect = true;
		try {
			xlvoPin::checkPin($_POST[self::F_PIN_INPUT]);
		} catch (xlvoVoterException $e) {
			xlvoInitialisation::resetCookiePIN();
			\ilUtil::sendFailure($this->txt('msg_validation_error_pin_' . $e->getCode()));
			$this->index();
			$redirect = false;
		}
		if ($redirect) {
			xlvoInitialisation::setCookiePIN($_POST[self::F_PIN_INPUT]);
			$this->ctrl->redirect($this, self::CMD_START_VOTER_PLAYER);
		}
	}


	protected function startVoterPlayer() {
		$this->initJsAndCss();
		$this->tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/default.css');
		$tpl = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voter/tpl.voter_player.html', true, false);
		$this->tpl->setContent($tpl->get());
	}


	protected function getVotingData() {
		/**
		 * @var $showAttendees xlvoVotingConfig
		 */
		$showAttendees = xlvoVotingConfig::find($this->manager->getVoting()->getObjId());
		if ($showAttendees->isShowAttendees()) {
			xlvoVoter::register($this->manager->getPlayer()->getId());
		}

		xlvoJsResponse::getInstance($this->manager->getPlayer()->getStdClassForVoter())->send();
	}


	protected function initJsAndCss() {
		require_once('./Services/jQuery/classes/class.iljQueryUtil.php');
		$this->tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voter/voter.css');
		\iljQueryUtil::initjQueryUI();
		\ilUtil::includeMathjax();
		$t = array( 'player_seconds' );

		$mathJaxSetting = new ilSetting("MathJax");

		/**
		 * @var $delay string
		 */
		$delay = xlvoConf::getConfig(xlvoConf::REQUEST_FREQUENCY);

		//check if we get some valid settings otherwise fall back to default value.
		if (is_numeric($delay)) {
			$delay = ((float)$delay) * 1000;
		} else {
			$delay = xlvoVoter::DEFAULT_CLIENT_UPDATE_DELAY * 1000;
		}

		$settings = array(
			'use_mathjax' => (bool)$mathJaxSetting->get("enable"),
			'debug'       => self::DEBUG,
			'ilias_51'    => version_compare(ILIAS_VERSION_NUMERIC, '5.1.00', '>'),
			'delay'       => $delay,
		);

		xlvoJs::getInstance()->api($this, array( 'ilUIPluginRouterGUI' ))->addSettings($settings)->name('Voter')->addTranslations($t)->init()
		      ->call('run');
		foreach (xlvoQuestionTypes::getActiveTypes() as $type) {
			xlvoQuestionTypesGUI::getInstance($this->manager, $type)->initJS();
		}
	}


	protected function getHTML() {
		$tpl = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voter/tpl.inner_screen.html', true, true);
		switch ($this->manager->getPlayer()->getStatus(true)) {
			case xlvoPlayer::STAT_STOPPED:
				$tpl->setVariable('TITLE', $this->txt('header_stopped'));
				$tpl->setVariable('DESCRIPTION', $this->txt('info_stopped'));
				$tpl->setVariable('COUNT', $this->manager->countVotings());
				$tpl->setVariable('POSITION', $this->manager->getVotingPosition());
				$tpl->setVariable('PIN', $this->manager->getVotingConfig()->getPin());
				break;
			case xlvoPlayer::STAT_RUNNING:
				$tpl->setVariable('TITLE', $this->manager->getVoting()->getTitle());
				$tpl->setVariable('DESCRIPTION', $this->manager->getVoting()->getDescription());
				$tpl->setVariable('COUNT', $this->manager->countVotings());
				$tpl->setVariable('POSITION', $this->manager->getVotingPosition());
				$tpl->setVariable('PIN', $this->manager->getVotingConfig()->getPin());

				$xlvoQuestionTypesGUI = xlvoQuestionTypesGUI::getInstance($this->manager);
				if ($xlvoQuestionTypesGUI->isShowQuestion()) {
					$tpl->setCurrentBlock('question_text');
					$tpl->setVariable('QUESTION_TEXT', $this->manager->getVoting()->getQuestionForPresentation());
					$tpl->parseCurrentBlock();
				}

				$tpl->setVariable('QUESTION', $xlvoQuestionTypesGUI->getMobileHTML());
				break;
			case xlvoPlayer::STAT_START_VOTING:
				$tpl->setVariable('TITLE', $this->txt('header_start'));
				$tpl->setVariable('DESCRIPTION', $this->txt('info_start'));
				$tpl->setVariable('GLYPH', xlvoGlyphGUI::get('pause'));
				break;
			case xlvoPlayer::STAT_END_VOTING:
				$tpl->setVariable('TITLE', $this->txt('header_end'));
				$tpl->setVariable('DESCRIPTION', $this->txt('info_end'));;
				$tpl->setVariable('GLYPH', xlvoGlyphGUI::get('stop'));
				break;
			case xlvoPlayer::STAT_FROZEN:
				$tpl->setVariable('TITLE', $this->txt('header_frozen'));
				$tpl->setVariable('DESCRIPTION', $this->txt('info_frozen'));
				$tpl->setVariable('COUNT', $this->manager->countVotings());
				$tpl->setVariable('POSITION', $this->manager->getVotingPosition());
				$tpl->setVariable('PIN', $this->manager->getVotingConfig()->getPin());
				$tpl->setVariable('GLYPH', xlvoGlyphGUI::get('pause'));
				break;
		}
		echo $tpl->get();
		exit;
	}
}
