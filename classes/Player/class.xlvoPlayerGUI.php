<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoDisplayPlayerGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingManager2.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoLinkButton.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoGlyphGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoDisplayPlayerGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/Modal/class.xlvoQRModalGUI.php');

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
	/**
	 * @var xlvoVotingManager2
	 */
	protected $manager;


	public function __construct() {
		parent::__construct();
		$this->manager = xlvoVotingManager2::getInstanceFromObjId(ilObject2::_lookupObjId($_GET['ref_id']));
		$this->tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/default.css');
	}


	/**
	 * @param $key
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('player_' . $key);
	}


	protected function index() {
		try {
			$this->manager->prepareStart();
		} catch (xlvoPlayerException $e) {
			ilUtil::sendFailure($this->txt('msg_no_start_' . $e->getCode()));

			return true;
		}

		$b = ilLinkButton::getInstance();
		$b->setCaption($this->txt('start_voting'), false);
		$b->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_PLAYER));
		$b->setId('btn-start-voting');
		$b->setPrimary(true);
		$this->toolbar->addButtonInstance($b);

		$b = ilLinkButton::getInstance();
		$b->setCaption($this->txt('start_voting_and_unfreeze'), false);
		$b->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_PLAYER_AND_UNFREEZE));
		$b->setId('btn-start-voting-unfreeze');
		$this->toolbar->addButtonInstance($b);

		$current_selection_list = $this->getVotingSelectionList(false);
		$this->toolbar->addText($current_selection_list->getHTML());

		$template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Player/tpl.start.html', true, true);
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
		xlvoJsResponse::getInstance(xlvoVoter::count($this->manager->getPlayer()->getId()))->send();
	}


	protected function startPlayerAnUnfreeze() {
		$this->initJS();
		$this->initToolbarDuringVoting();
		$this->manager->getPlayer()->unfreeze();
		$modal = xlvoQRModalGUI::getInstanceFromVotingConfig($this->manager->getVotingConfig())->getHTML();
		$this->tpl->setContent($modal . $this->getPlayerHTML());
		$this->handlePreview();
	}


	protected function startPlayer() {
		$settings = array(
			'status_running' => xlvoPlayer::STAT_RUNNING,
		);
		if ($_GET[self::IDENTIFIER]) {
			$this->manager->open($_GET[self::IDENTIFIER]);
		}
		$this->initJS($settings);
		$this->initToolbarDuringVoting();
		$modal = xlvoQRModalGUI::getInstanceFromVotingConfig($this->manager->getVotingConfig())->getHTML();
		$this->tpl->setContent($modal . $this->getPlayerHTML());
		$this->handlePreview();
	}


	protected function getPlayerData() {
		$this->manager->getPlayer()->attend();
		$player = $this->manager->getPlayer()->getStdClassForPlayer();
		$results = array(
			'player'      => $player,
			'player_html' => $this->getPlayerHTML(true),
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
		}
		xlvoJsResponse::getInstance(true)->send();
	}


	/**
	 * Set Toolbar Content and Buttons for the Player.
	 */
	protected function initToolbarDuringVoting() {
		// Freeze
		$b = xlvoLinkButton::getInstance();
		$b->clearClasses();
		$b->addCSSClass('btn-warning');
		$b->setCaption(xlvoGlyphGUI::get('pause') . $this->txt('freeze'), false);
		$b->setUrl('#');
		$b->setId('btn-freeze');
		if (method_exists($this->toolbar, 'addStickyItem')) { // Only ILIAS 5.1
			$this->toolbar->addStickyItem($b);
		} else {
			$this->toolbar->addButtonInstance($b);
		}

		// Unfreeze
		$b = ilLinkButton::getInstance();
		$b->setPrimary(true);
		$b->setCaption(xlvoGlyphGUI::get('play') . $this->txt('unfreeze'), false);
		$b->setUrl('#');
		$b->setId('btn-unfreeze');
		$this->toolbar->addButtonInstance($b);

		// Hide
		$b = ilLinkButton::getInstance();
		$b->setCaption(xlvoGlyphGUI::get('eye-close') . $this->txt('hide_results'), false);
		$b->setUrl('#');
		$b->setId('btn-hide-results');
		$this->toolbar->addButtonInstance($b);

		// Show
		$b = ilLinkButton::getInstance();
		$b->setCaption(xlvoGlyphGUI::get('eye-open') . $this->txt('show_results'), false);
		$b->setUrl('#');
		$b->setId('btn-show-results');
		$this->toolbar->addButtonInstance($b);

		// Reset
		$b = ilLinkButton::getInstance();
		$b->setCaption(xlvoGlyphGUI::get('remove') . $this->txt('reset'), false);
		$b->setUrl('#');
		$b->setId('btn-reset');
		$this->toolbar->addButtonInstance($b);

		//
		//
		$this->toolbar->addSeparator();
		//
		//

		// PREV
		$b = ilLinkButton::getInstance();
		$b->setDisabled(true);
		$b->setUrl($this->ctrl->getLinkTarget($this, self::CMD_PREVIOUS));
		$b->setCaption(xlvoGlyphGUI::get(xlvoGlyphGUI::PREVIOUS), false);
		$b->setId('btn-previous');
		$this->toolbar->addButtonInstance($b);

		// NEXT
		$b = ilLinkButton::getInstance();
		$b->setDisabled(true);
		$b->setCaption(xlvoGlyphGUI::get(xlvoGlyphGUI::NEXT), false);
		$b->setUrl($this->ctrl->getLinkTarget($this, self::CMD_NEXT));
		$b->setId('btn-next');
		$this->toolbar->addButtonInstance($b);

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
			$b = ilLinkButton::getInstance();
			$b->setCaption(xlvoGlyphGUI::get('fullscreen'), false);
			$b->setUrl('#');
			$b->setId('btn-start-fullscreen');
			$this->toolbar->addButtonInstance($b);

			$b = ilLinkButton::getInstance();
			$b->setCaption(xlvoGlyphGUI::get('resize-small'), false);
			$b->setUrl('#');
			$b->setId('btn-close-fullscreen');
			$this->toolbar->addButtonInstance($b);
		}

		// END
		$b = ilLinkButton::getInstance();
		$b->setCaption(xlvoGlyphGUI::get('stop') . $this->txt('terminate'), false);
		$b->setUrl($this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_TERMINATE));
		$b->setId('btn-terminate');
		$this->toolbar->addButtonInstance($b);
	}


	/**
	 * @param bool $async
	 * @return ilAdvancedSelectionListGUI
	 */
	protected function getVotingSelectionList($async = true) {
		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->txt('voting_list'));
		$current_selection_list->setId('xlvo_select');
		$current_selection_list->setTriggerEvent('xlvo_voting');
		$current_selection_list->setUseImages(false);
		/**
		 * @var xlvoVoting[] $votings
		 */
		foreach ($this->manager->getAllVotings() as $voting) {
			$this->ctrl->setParameter(new xlvoPlayerGUI(), self::IDENTIFIER, $voting->getId());
			if ($async) {
				$current_selection_list->addItem($voting->getTitle(), $voting->getId(), $this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_PLAYER), '', '', '', '', false, 'xlvoPlayer.open('
				                                                                                                                                                                        . $voting->getId()
				                                                                                                                                                                        . ')');
			} else {
				$current_selection_list->addItem($voting->getTitle(), $voting->getId(), $this->ctrl->getLinkTarget(new xlvoPlayerGUI(), self::CMD_START_PLAYER));
			}
		}

		return $current_selection_list;
	}


	protected function initJS() {
		$settings = array(
			'status_running' => xlvoPlayer::STAT_RUNNING,
			'identifier'     => self::IDENTIFIER,
		);
		if ($this->manager->getVotingConfig()->isKeyboardActive()) {
			$keyboard = new stdClass();
			$keyboard->toggle_results = 9;
			$keyboard->toggle_freeze = 32;
			$keyboard->previous = 37;
			$keyboard->next = 39;
			$settings['keyboard'] = $keyboard;
		}
		iljQueryUtil::initjQuery();
		xlvoJs::getInstance()->addLibToHeader('screenfull.min.js');
		xlvoJs::getInstance()->addLibToHeader('screenfull.min.js');
		xlvoJs::getInstance()->ilias($this)->addSettings($settings)->name('Player')->addTranslations(array(
			'player_voters_online',
			'voting_confirm_reset',
		))->init()->call('run');
	}


	protected function handlePreview() {
		if ($this->manager->getVotingConfig()->isSelfVote()) {
			$preview = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Player/tpl.preview.html', true, false);
			$preview->setVariable('URL', xlvoConf::getShortLinkURL() . $this->manager->getVotingConfig()->getPin());
			$this->tpl->setRightContent($preview->get());
		}
	}
}
