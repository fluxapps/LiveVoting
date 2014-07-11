<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/GroupedListDropdown/class.ctrlmmEntryGroupedListDropdownGUI.php');
require_once('./Services/UIComponent/GroupedList/classes/class.ilGroupedListGUI.php');
require_once('./Services/Tracking/classes/class.ilObjUserTracking.php');

/**
 * ctrlmmEntryDesktopGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
class ctrlmmEntryDesktopGUI extends ctrlmmEntryGroupedListDropdownGUI {

	const F_SHOW_LOGOUT = 'show_logout';
	/**
	 * @var ctrlmmEntryDesktop
	 */
	public $entry;
	/**
	 * @var bool
	 */
	protected $mail = false;


	/**
	 * @param ctrlmmEntry $entry
	 * @param null        $parent_gui
	 */
	public function __construct(ctrlmmEntry $entry, $parent_gui = NULL) {
		global $rbacsystem, $ilUser;
		parent::__construct($entry, $parent_gui);
		$this->mail = ($rbacsystem->checkAccess('internal_mail', ilMailGlobalServices::getMailObjectRefId()) AND
			$ilUser->getId() != ANONYMOUS_USER_ID);
	}


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return html
	 */
	public function setGroupedListContent() {
		global $lng, $ilSetting, $rbacsystem, $ilias;

		$this->gl->addEntry($lng->txt('overview'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToSelectedItems', '_top', '', '', 'mm_pd_sel_items', ilHelp::getMainMenuTooltip('mm_pd_sel_items'), 'left center', 'right center', false);

		// my groups and courses, if both is available
		if ($ilSetting->get('disable_my_offers') == 0 AND $ilSetting->get('disable_my_memberships') == 0
		) {
			$this->gl->addEntry($lng->txt('my_courses_groups'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToMemberships', '_top', '', '', 'mm_pd_crs_grp', ilHelp::getMainMenuTooltip('mm_pd_crs_grp'), 'left center', 'right center', false);
		}

		// bookmarks
		if (! $ilias->getSetting('disable_bookmarks')) {
			$this->gl->addEntry($lng->txt('bookmarks'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToBookmarks', '_top', '', '', 'mm_pd_bookm', ilHelp::getMainMenuTooltip('mm_pd_bookm'), 'left center', 'right center', false);
		}

		// private notes
		if (! $ilias->getSetting('disable_notes')) {
			$this->gl->addEntry($lng->txt('notes_and_comments'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToNotes', '_top', '', '', 'mm_pd_notes', ilHelp::getMainMenuTooltip('mm_pd_notes'), 'left center', 'right center', false);
		}

		// news
		if ($ilSetting->get('block_activated_news')) {
			$this->gl->addEntry($lng->txt('news'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToNews', '_top', '', '', 'mm_pd_news', ilHelp::getMainMenuTooltip('mm_pd_news'), 'left center', 'right center', false);
		}

		// overview is always active
		$this->gl->addSeparator();

		$separator = false;

		if (! $ilSetting->get('disable_personal_workspace')) {
			// workspace
			$this->gl->addEntry($lng->txt('personal_workspace'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToWorkspace', '_top', '', '', 'mm_pd_wsp', ilHelp::getMainMenuTooltip('mm_pd_wsp'), 'left center', 'right center', false);

			$separator = true;
		}

		// portfolio
		if ($ilSetting->get('user_portfolios')) {
			$this->gl->addEntry($lng->txt('portfolio'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToPortfolio', '_top', '', '', 'mm_pd_port', ilHelp::getMainMenuTooltip('mm_pd_port'), 'left center', 'right center', false);

			$separator = true;
		}

		// skills
		$skmg_set = new ilSetting('skmg');
		if ($skmg_set->get('enable_skmg')) {
			$this->gl->addEntry($lng->txt('skills'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToSkills', '_top', '', '', 'mm_pd_skill', ilHelp::getMainMenuTooltip('mm_pd_skill'), 'left center', 'right center', false);

			$separator = true;
		}

		// Learning Progress
		if (ilObjUserTracking::_enabledLearningProgress() AND (ilObjUserTracking::_hasLearningProgressOtherUsers()
				OR ilObjUserTracking::_hasLearningProgressLearner())
		) {
			//$ilTabs->addTarget('learning_progress', $this->ctrl->getLinkTargetByClass('ilLearningProgressGUI'));
			$this->gl->addEntry($lng->txt('learning_progress'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToLP', '_top', '', '', 'mm_pd_lp', ilHelp::getMainMenuTooltip('mm_pd_lp'), 'left center', 'right center', false);

			$separator = true;
		}

		if ($separator) {
			$this->gl->addSeparator();
		}

		$separator = false;

		// calendar
		$settings = ilCalendarSettings::_getInstance();
		if ($settings->isEnabled()) {
			$this->gl->addEntry($lng->txt('calendar'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToCalendar', '_top', '', '', 'mm_pd_cal', ilHelp::getMainMenuTooltip('mm_pd_cal'), 'left center', 'right center', false);

			$separator = true;
		}

		// mail
		if ($this->mail) {
			$this->gl->addEntry($lng->txt('mail'), 'ilias.php?baseClass=ilMailGUI', '_top', '', '', 'mm_pd_mail', ilHelp::getMainMenuTooltip('mm_pd_mail'), 'left center', 'right center', false);

			$separator = true;
		}

		// contacts
		if (! $ilias->getSetting('disable_contacts') AND ($ilias->getSetting('disable_contacts_require_mail')
				OR $rbacsystem->checkAccess('internal_mail', ilMailGlobalServices::getMailObjectRefId()))
		) {
			$this->gl->addEntry($lng->txt('mail_addressbook'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToContacts', '_top', '', '', 'mm_pd_contacts', ilHelp::getMainMenuTooltip('mm_pd_contacts'), 'left center', 'right center', false);

			$separator = true;
		}

		if ($separator) {
			$this->gl->addSeparator();
		}

		// profile
		$this->gl->addEntry($lng->txt('personal_profile'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToProfile', '_top', '', '', 'mm_pd_profile', ilHelp::getMainMenuTooltip('mm_pd_profile'), 'left center', 'right center', false);

		// settings
		$this->gl->addEntry($lng->txt('personal_settings'), 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;cmd=jumpToSettings', '_top', '', '', 'mm_pd_sett', ilHelp::getMainMenuTooltip('mm_pd_sett'), 'left center', 'right center', false);

		if ($this->entry->getShowLogout()) {
			$this->gl->addSeparator();
			// settings
			$this->gl->addEntry($lng->txt('logout'), 'logout.php', '_top', '', '', '', false, 'left center', 'right center', false);
		}
	}



	//
	// FORM
	//

	/**
	 * @param string $mode
	 */
	public function initForm($mode = 'create') {
		parent::initForm($mode);
		$te = new ilCheckboxInputGUI($this->pl->txt(self::F_SHOW_LOGOUT), self::F_SHOW_LOGOUT);
		$this->form->addItem($te);
	}


	public function setFormValuesByArray() {
		$values = parent::setFormValuesByArray();
		$values[self::F_SHOW_LOGOUT] = $this->entry->getShowLogout();
		$this->form->setValuesByArray($values);
	}


	public function createEntry() {
		parent::createEntry();
		$this->entry->setShowLogout($this->form->getInput(self::F_SHOW_LOGOUT));
		$this->entry->update();
	}
}

?>
