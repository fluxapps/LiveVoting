<?php

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Js\xlvoJs;
use LiveVoting\Js\xlvoJsResponse;
use LiveVoting\Player\QR\xlvoQR;
use LiveVoting\Player\xlvoPlayer;
use LiveVoting\Player\xlvoPlayerException;
use LiveVoting\Voter\xlvoVoter;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Voting\xlvoVotingManager2;
use LiveVoting\xlvoLinkButton;

require_once('./Services/Administration/classes/class.ilSetting.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once("./include/inc.ilias_version.php");
require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.ILIASVersionEnum.php");

/**
 * Class xlvoPlayerGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoPlayerGUI extends xlvoGUI {

	const IDENTIFIER = 'xvi';
	const CMD_START_PLAYER = 'startPlayer';
	const CMD_START_PLAYER_AND_UNFREEZE = 'startPlayerAnUnfreeze';
	const CMD_NEXT = 'next';
	const CMD_PREVIOUS = 'previous';
	const CMD_FREEZE = 'freeze';
	const CMD_UNFREEZE = 'unfreeze';
	const CMD_RESET = 'reset';
	const CMD_TERMINATE = 'terminate';
	const CMD_END = 'end';
	const CMD_GET_PLAYER_DATA = 'getPlayerData';
	const CMD_API_CALL = 'apiCall';
	const DEBUG = false;
	/**
	 * @var xlvoVotingManager2
	 */
	protected $manager;


	public function __construct() {
		parent::__construct();
		$this->manager = xlvoVotingManager2::getInstanceFromObjId(\ilObject2::_lookupObjId($_GET['ref_id']));
		$this->tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/default.css');
	}


	/**
	 * @param $key
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('player_' . $key);
	}


    /**
     * @return bool
     */
    protected function index() {
		try {
			$this->manager->prepareStart();
		} catch (xlvoPlayerException $e) {
			\ilUtil::sendFailure($this->txt('msg_no_start_' . $e->getCode()));

			return true;
		}

		$b = \ilLinkButton::getInstance();
		$b->setCaption($this->txt('start_voting'), false);
		$b->addCSSClass('xlvo-preview');
		$b->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_PLAYER));
		$b->setId('btn-start-voting');
		$b->setPrimary(true);
		$this->toolbar->addButtonInstance($b);

		$b = \ilLinkButton::getInstance();
		$b->setCaption($this->txt('start_voting_and_unfreeze'), false);
		$b->addCSSClass('xlvo-preview');
		$b->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_PLAYER_AND_UNFREEZE));
		$b->setId('btn-start-voting-unfreeze');
		$this->toolbar->addButtonInstance($b);

		$current_selection_list = $this->getVotingSelectionList(false);
		$this->toolbar->addText($current_selection_list->getHTML());

		$template = new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Player/tpl.start.html', true, true);
		/**
		 * @var xlvoVotingConfig $xlvoVotingConfig
		 */
		$xlvoVotingConfig = $this->manager->getVotingConfig();
		$template->setVariable('PIN', $xlvoVotingConfig->getPin());

		$short_link = xlvoConf::getShortLinkURL() . $xlvoVotingConfig->getPin();
		$template->setVariable('QR-CODE', xlvoQR::getImageDataString($short_link, 180));
		$template->setVariable('SHORTLINK', $short_link);
		$template->setVariable('MODAL', xlvoQRModalGUI::getInstanceFromVotingConfig($xlvoVotingConfig)->getHTML());

		$js = xlvoJs::getInstance()->ilias($this)->name('Player')->init();
		if ($this->manager->getVotingConfig()->isShowAttendees()) {
			$js->call('updateAttendees');
			$template->touchBlock('attendees');
		}
		$js->call('handleStartButton');

		$this->tpl->setContent($template->get());
	}

    protected function getAttendees() {
        xlvoJsResponse::getInstance(xlvoVoter::countVoters($this->manager->getPlayer()->getId()))->send();
    }

	protected function startPlayerAnUnfreeze() {
		$this->initJSandCss();
		$this->initToolbarDuringVoting();
		$this->manager->prepare();
		$this->manager->getPlayer()->unfreeze();
		$modal = xlvoQRModalGUI::getInstanceFromVotingConfig($this->manager->getVotingConfig())->getHTML();
		$this->tpl->setContent($modal . $this->getPlayerHTML());
		$this->handlePreview();
	}


	protected function startPlayer() {
		if ($_GET[self::IDENTIFIER]) {
			$this->manager->open($_GET[self::IDENTIFIER]);
		}
		$this->initJSandCss();
		$this->manager->prepare();
		$this->initToolbarDuringVoting();
		$modal = xlvoQRModalGUI::getInstanceFromVotingConfig($this->manager->getVotingConfig())->getHTML();
		$this->tpl->setContent($modal . $this->getPlayerHTML());
		$this->handlePreview();
	}


	protected function getPlayerData() {
		$this->manager->attend();
		$results = array(
			'player'       => $this->manager->getPlayer()->getStdClassForPlayer(),
			'player_html'  => $this->getPlayerHTML(true),
			'buttons_html' => $this->getButtonsHTML(),
		);
		xlvoJsResponse::getInstance($results)->send();
	}


	/**
	 * @param bool $inner
	 * @return string
	 */
	protected function getPlayerHTML($inner = false) {
		$xlvoDisplayPlayerGUI = new xlvoDisplayPlayerGUI($this->manager);

		return $xlvoDisplayPlayerGUI->getHTML($inner);
	}


	/**
	 * @return string
	 */
	protected function getButtonsHTML() {
		// Buttons from Questions
		$xlvoQuestionTypesGUI = xlvoQuestionTypesGUI::getInstance($this->manager);
		if ($xlvoQuestionTypesGUI->hasButtons()) {
			$toolbar = new xlvoToolbarGUI();

			foreach ($xlvoQuestionTypesGUI->getButtonInstances() as $buttonInstance) {
				if ($buttonInstance instanceof \ilButton || $buttonInstance instanceof \ilButtonBase) {
					$toolbar->addButtonInstance($buttonInstance);
				}
			}

			return $toolbar->getHTML();
		}

		return '';
	}


	protected function next() {
		$this->manager->next();
		$this->ctrl->redirect($this, self::CMD_START_PLAYER);
	}


	protected function previous() {
		$this->manager->previous();
		$this->ctrl->redirect($this, self::CMD_START_PLAYER);
	}


	protected function terminate() {
		$this->manager->terminate();
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	protected function apiCall() {
		$return_value = true;
		switch ($_POST['call']) {
			case 'toggle_freeze':
				$this->manager->getPlayer()->toggleFreeze();
				break;
			case 'toggle_results':
				$this->manager->getPlayer()->toggleResults();
				break;
			case 'reset':
				$this->manager->reset();
				break;
			case 'next':
				$this->manager->next();
				break;
			case 'previous':
				$this->manager->previous();
				break;
			case 'open':
				$this->manager->open($_POST[self::IDENTIFIER]);
				break;
			case 'countdown':
				$this->manager->countdown($_POST['seconds']);
				break;
			case 'button':
				/**
				 * QuestionGUIs can add own button which have to call the player with 'call=button&button_id={cmd}&data=[some,data]
				 */
				$xlvoQuestionTypesGUI = xlvoQuestionTypesGUI::getInstance($this->manager);
				$xlvoQuestionTypesGUI->handleButtonCall($_POST['button_id'], $_POST['button_data']);
				$return_value = new \stdClass();
				$return_value->buttons_html = $this->getButtonsHTML();
				break;
		}
		xlvoJsResponse::getInstance($return_value)->send();
	}


	/**
	 * Set Toolbar Content and Buttons for the Player.
	 */
	protected function initToolbarDuringVoting() {
		require_once('./Services/UIComponent/SplitButton/classes/class.ilButtonToSplitButtonMenuItemAdapter.php');
		require_once('./Services/UIComponent/SplitButton/classes/class.ilSplitButtonGUI.php');

		// Freeze
		$suspendButton = xlvoLinkButton::getInstance();
		$suspendButton->clearClasses();
		$suspendButton->addCSSClass('btn-warning');
		$suspendButton->setCaption(xlvoGlyphGUI::get('pause') . $this->txt('freeze'), false);
		$suspendButton->setUrl('#');
		$suspendButton->setId('btn-freeze');
		$this->addStickyButtonToToolbar($suspendButton);

		// Unfreeze
		$playButton = xlvoLinkButton::getInstance();
		$playButton->clearClasses();
		$playButton->setPrimary(true);
		$playButton->setCaption(xlvoGlyphGUI::get('play') . $this->txt('unfreeze'), false);
		$playButton->setUrl('#');
		$playButton->setId('btn-unfreeze');

			$split = ilSplitButtonGUI::getInstance();
			$split->setDefaultButton($playButton);
			foreach (array( 10, 30, 90, 120, 180, 240, 300 ) as $seconds) {
				$cd = ilLinkButton::getInstance();
				$cd->setUrl('#');
				$cd->setCaption($seconds . ' ' . $this->pl->txt('player_seconds'), false);
				$cd->setOnClick("xlvoPlayer.countdown($seconds);");
				$ilSplitButtonMenuItem = new ilButtonToSplitButtonMenuItemAdapter($cd);
				$split->addMenuItem($ilSplitButtonMenuItem);
			}

			$this->addStickyButtonToToolbar($split);

		// Hide
		$suspendButton = ilLinkButton::getInstance();
		$suspendButton->setCaption($this->txt('hide_results'), false);
		$suspendButton->setUrl('#');
		$suspendButton->setId('btn-hide-results');
		$this->toolbar->addButtonInstance($suspendButton);

		// Show
		$suspendButton = ilLinkButton::getInstance();
		$suspendButton->setCaption($this->txt('show_results'), false);
		$suspendButton->setUrl('#');
		$suspendButton->setId('btn-show-results');
		$this->toolbar->addButtonInstance($suspendButton);

		// Reset
		$suspendButton = ilLinkButton::getInstance();
		$suspendButton->setCaption(xlvoGlyphGUI::get('remove') . $this->txt('reset'), false);
		$suspendButton->setUrl('#');
		$suspendButton->setId('btn-reset');
		$this->toolbar->addButtonInstance($suspendButton);

		//
		//
		$this->toolbar->addSeparator();
		//
		//

		// PREV
		$suspendButton = ilLinkButton::getInstance();
		$suspendButton->setDisabled(true);
		$suspendButton->setUrl($this->ctrl->getLinkTarget($this, self::CMD_PREVIOUS));
		$suspendButton->setCaption(xlvoGlyphGUI::get(xlvoGlyphGUI::PREVIOUS), false);
		$suspendButton->setId('btn-previous');
		$this->toolbar->addButtonInstance($suspendButton);

		// NEXT
		$suspendButton = ilLinkButton::getInstance();
		$suspendButton->setDisabled(true);
		$suspendButton->setCaption(xlvoGlyphGUI::get(xlvoGlyphGUI::NEXT), false);
		$suspendButton->setUrl($this->ctrl->getLinkTarget($this, self::CMD_NEXT));
		$suspendButton->setId('btn-next');
		$this->toolbar->addButtonInstance($suspendButton);

		// Votings
		$current_selection_list = $this->getVotingSelectionList();
		$this->toolbar->addText($current_selection_list->getHTML());

		//
		//
		$this->toolbar->addSeparator();
		//
		//

		// Fullscreen
		if ($this->manager->getVotingConfig()->isFullScreen()) {
			$suspendButton = ilLinkButton::getInstance();
			$suspendButton->setCaption(xlvoGlyphGUI::get('fullscreen'), false);
			$suspendButton->setUrl('#');
			$suspendButton->setId('btn-start-fullscreen');
			$this->toolbar->addButtonInstance($suspendButton);

			$suspendButton = ilLinkButton::getInstance();
			$suspendButton->setCaption(xlvoGlyphGUI::get('resize-small'), false);
			$suspendButton->setUrl('#');
			$suspendButton->setId('btn-close-fullscreen');
			$this->toolbar->addButtonInstance($suspendButton);
		}

		// END
		$suspendButton = ilLinkButton::getInstance();
		$suspendButton->setCaption(xlvoGlyphGUI::get('stop') . $this->txt('terminate'), false);
		$suspendButton->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_TERMINATE));
		$suspendButton->setId('btn-terminate');
		$this->toolbar->addButtonInstance($suspendButton);
		if (self::DEBUG) {

			// PAUSE PULL
			$suspendButton = ilLinkButton::getInstance();
			$suspendButton->setCaption('Toogle Pulling', false);
			$suspendButton->setUrl('#');
			$suspendButton->setId('btn-toggle-pull');
			$this->toolbar->addButtonInstance($suspendButton);
		}
	}


	/**
	 * Adds a button to the toolbar and make it stick to it,
	 * which means that the button is also visible if the mobile size of the website is used.
	 *
	 * @param ilButtonBase $button Button which should be added sticky to the toolbar.
	 *
	 * @return void
	 */
	private function addStickyButtonToToolbar(ilButtonBase $button) {

		//check if the new methods are usable
		if (method_exists($this->toolbar, 'addStickyItem')) {
			$this->toolbar->addStickyItem($button);
		} else {
			// ILIAS 5.1 fallback
			$this->toolbar->addButtonInstance($button);
		}
		
	}


	/**
	 * @param bool $async
	 * @return ilAdvancedSelectionListGUI
	 */
	protected function getVotingSelectionList($async = true) {
		$current_selection_list = new \ilAdvancedSelectionListGUI();
		$current_selection_list->setItemLinkClass('xlvo-preview');
		$current_selection_list->setListTitle($this->txt('voting_list'));
		$current_selection_list->setId('xlvo_select');
		$current_selection_list->setTriggerEvent('xlvo_voting');
		$current_selection_list->setUseImages(false);
		/**
		 * @var xlvoVoting[] $votings
		 */
		foreach ($this->manager->getAllVotings() as $voting) {
			$id = $voting->getId();
			$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $id);
			$t = $voting->getTitle();
			$target = $this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_PLAYER);
			if ($async) {
				$current_selection_list->addItem($t, $id, $target, '', '', '', '', false, 'xlvoPlayer.open(' . $id . ')');
			} else {
				$current_selection_list->addItem($t, $id, $target);
			}
		}

		return $current_selection_list;
	}


	protected function initJSandCss() {
		$subversion = (int)explode('.', ILIAS_VERSION_NUMERIC)[1];

		switch ($subversion) {
			case \LiveVoting\Context\ILIASVersionEnum::ILIAS_VERSION_5_2:
			case \LiveVoting\Context\ILIASVersionEnum::ILIAS_VERSION_5_3:
				include_once './Services/MathJax/classes/class.ilMathJax.php';
				ilMathJax::getInstance()->includeMathJax();
				break;
			default:
				throw new ilException('Can not initialise js and css for the installed ILIAS version.');
				break;
		}

		$mathJaxSetting = new ilSetting("MathJax");
		$settings = array(
			'status_running' => xlvoPlayer::STAT_RUNNING,
			'identifier'     => self::IDENTIFIER,
			'use_mathjax'    => (bool)$mathJaxSetting->get("enable"),
			'debug'          => self::DEBUG
		);
		$keyboard = new stdClass();
		$keyboard->active = $this->manager->getVotingConfig()->isKeyboardActive();
		if ($keyboard->active) {
			$keyboard->toggle_results = 9;
			$keyboard->toggle_freeze = 32;
			$keyboard->previous = 37;
			$keyboard->next = 39;
		}
		$settings['keyboard'] = $keyboard;
		iljQueryUtil::initjQuery();
		xlvoJs::getInstance()->addLibToHeader('screenfull.min.js');
		xlvoJs::getInstance()->addLibToHeader('screenfull.min.js');
		xlvoJs::getInstance()->ilias($this)->addSettings($settings)->name('Player')->addTranslations(array(
			'player_voters_online',
			'voting_confirm_reset',
		))->init()->call('run');
		$this->tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Player/player.css');
		$this->tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/bar.css');
	}


	protected function handlePreview() {
		if ($this->manager->getVotingConfig()->isSelfVote()) {
			$preview = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Player/tpl.preview.html', true, false);
			$preview->setVariable('URL', xlvoConf::getShortLinkURL() . $this->manager->getVotingConfig()->getPin());
			$this->tpl->setRightContent($preview->get());
		}
	}
}
