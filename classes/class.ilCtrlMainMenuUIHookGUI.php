<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once('./Services/UIComponent/classes/class.ilUIHookPluginGUI.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ctrlmmMenuGUI.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ctrlmmMenu.php');
require_once('class.ilCtrlMainMenuPlugin.php');
require_once('./Modules/SystemFolder/classes/class.ilObjSystemFolder.php');
require_once('class.ilCtrlMainMenuConfig.php');

/**
 * User interface hook class
 *
 * @author            Alex Killing <alex.killing@gmx.de>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           2.0.02
 * @ingroup           ServicesUIComponent
 * @ilCtrl_IsCalledBy ilCtrlMainMenuUIHookGUI: ilAdministrationGUI, ilPersonalDesktopGUI, ilRepositoryGUI, ilObjPluginDispatchGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilCtrlMainMenuUIHookGUI: ilAdministrationGUI, ilPersonalDesktopGUI, ilRepositoryGUI, ilObjPluginDispatchGUI, ilCommonActionDispatcherGUI
 */
class ilCtrlMainMenuUIHookGUI extends ilUIHookPluginGUI {

	/**
	 * @var bool
	 */
	protected static $replaced = false;
	protected $pl;

	public function __construct() {
		$this->pl = ilCtrlMainMenuPlugin::getInstance();
	}


	/**
	 * @param       $a_comp
	 * @param       $a_part
	 * @param array $a_par
	 *
	 * @return array
	 */
	public function getHTML($a_comp, $a_part, $a_par = array()) {

		$full_header = ($a_part == 'template_get' AND $a_par['tpl_id'] == 'Services/MainMenu/tpl.main_menu.html');
		$replace = (bool)ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_REPLACE_FULL_HEADER);
		if ($full_header AND !self::$replaced) {
			if ($full_header AND $replace) {
				self::$replaced = true;

				if (ctrlmm::is50()) {
					return array(
						'mode' => ilUIHookPluginGUI::REPLACE,
						'html' => $this->getMainMenuHTML50()
					);
				} else {
					return array(
						'mode' => ilUIHookPluginGUI::REPLACE,
						'html' => $this->getMainMenuHTML()
					);
				}
			}
		}

		$menu_only = ($a_comp == 'Services/MainMenu' AND $a_part == 'main_menu_list_entries');
		if ($menu_only AND !self::$replaced AND !$replace) {
			$mm = new ctrlmmMenuGUI(0);
			self::$replaced = true;

			return array(
				'mode' => ilUIHookPluginGUI::REPLACE,
				'html' => $mm->getHTML()
			);
		}

		return array( 'mode' => ilUIHookPluginGUI::KEEP, 'html' => '' );
	}


	/**
	 * @return string
	 */
	protected function getMainMenuHTML50() {
		global $ilUser;

		$tpl = ilCtrlMainMenuPlugin::getInstance()->getVersionTemplate('tpl.mainmenu.html', false, false);

		$tpl->setVariable("CSS_PREFIX", ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_CSS_PREFIX));

		$tpl->setVariable("HEADER_URL", $this->getHeaderURL());
		$tpl->setVariable("HEADER_ICON", ilUtil::getImagePath("HeaderIcon.png"));
		$mm = new ctrlmmMenuGUI(0);
		$tpl->setVariable("MAIN_MENU_LEFT", $mm->getHTML());
		$mm = new ctrlmmMenuGUI(0);
		$mm->setSide(ctrlmmMenuGUI::SIDE_RIGHT);
        $mm->setCssId('ilTopBarNav');
		$tpl->setVariable("MAIN_MENU_RIGHT", $mm->getHTML());

		$notificationSettings = new ilSetting('notifications');
		$chatSettings = new ilSetting('chatroom');

        /*require_once 'Services/Notifications/classes/class.ilNotificationOSDHandler.php';
		$notifications = ilNotificationOSDHandler::getNotificationsForUser($ilUser->getId());
		$tpl->setVariable('INITIAL_NOTIFICATIONS', json_encode($notifications));
		$tpl->setVariable('OSD_POLLING_INTERVALL', $notificationSettings->get('osd_polling_intervall') ? $notificationSettings->get('osd_polling_intervall') : '5');
		$tpl->setVariable('OSD_PLAY_SOUND',
			$chatSettings->get('play_invitation_sound') && $ilUser->getPref('chat_play_invitation_sound') ? 'true' : 'false');
		foreach ($notifications as $notification) {
			if ($notification['type'] == 'osd_maint') {
				continue;
			}
			$tpl->setCurrentBlock('osd_notification_item');

			$tpl->setVariable('NOTIFICATION_ICON_PATH', $notification['data']->iconPath);
			$tpl->setVariable('NOTIFICATION_TITLE', $notification['data']->title);
			$tpl->setVariable('NOTIFICATION_LINK', $notification['data']->link);
			$tpl->setVariable('NOTIFICATION_LINKTARGET', $notification['data']->linktarget);
			$tpl->setVariable('NOTIFICATION_ID', $notification['notification_osd_id']);
			$tpl->setVariable('NOTIFICATION_SHORT_DESCRIPTION', $notification['data']->shortDescription);
			$tpl->parseCurrentBlock();
		}*/

		$ilObjSystemFolder = new ilObjSystemFolder(SYSTEM_FOLDER_ID);
		$header_top_title = $ilObjSystemFolder->_getHeaderTitle();
		$tpl->setVariable("TXT_HEADER_TITLE", $header_top_title);

		return $tpl->get();
	}


	/**
	 * @return string
	 */
	protected function getMainMenuHTML() {
		global $ilUser;

		$tpl = ilCtrlMainMenuPlugin::getInstance()->getVersionTemplate('tpl.mainmenu.html', false, false);

		$tpl->setVariable("CSS_PREFIX", ctrlmmMenu::getCssPrefix());

		$tpl->setVariable("HEADER_URL", $this->getHeaderURL());
		$tpl->setVariable("HEADER_ICON", ilUtil::getImagePath("HeaderIcon.png"));
		$mm = new ctrlmmMenuGUI(0);
		$tpl->setVariable("MAIN_MENU_LEFT", $mm->getHTML());
		$mm = new ctrlmmMenuGUI(0);
		$mm->setSide(ctrlmmMenuGUI::SIDE_RIGHT);
		$tpl->setVariable("MAIN_MENU_RIGHT", $mm->getHTML());

		$notificationSettings = new ilSetting('notifications');
		$chatSettings = new ilSetting('chatroom');
		require_once 'Services/Notifications/classes/class.ilNotificationOSDHandler.php';
		$notifications = ilNotificationOSDHandler::getNotificationsForUser($ilUser->getId());
		$tpl->setVariable('INITIAL_NOTIFICATIONS', json_encode($notifications));
		$tpl->setVariable('OSD_POLLING_INTERVALL', $notificationSettings->get('osd_polling_intervall') ? $notificationSettings->get('osd_polling_intervall') : '5');
		$tpl->setVariable('OSD_PLAY_SOUND',
			$chatSettings->get('play_invitation_sound') && $ilUser->getPref('chat_play_invitation_sound') ? 'true' : 'false');
		foreach ($notifications as $notification) {
			if ($notification['type'] == 'osd_maint') {
				continue;
			}
			$tpl->setCurrentBlock('osd_notification_item');

			$tpl->setVariable('NOTIFICATION_ICON_PATH', $notification['data']->iconPath);
			$tpl->setVariable('NOTIFICATION_TITLE', $notification['data']->title);
			$tpl->setVariable('NOTIFICATION_LINK', $notification['data']->link);
			$tpl->setVariable('NOTIFICATION_LINKTARGET', $notification['data']->linktarget);
			$tpl->setVariable('NOTIFICATION_ID', $notification['notification_osd_id']);
			$tpl->setVariable('NOTIFICATION_SHORT_DESCRIPTION', $notification['data']->shortDescription);
			$tpl->parseCurrentBlock();
		}

		$ilObjSystemFolder = new ilObjSystemFolder(SYSTEM_FOLDER_ID);
		$header_top_title = $ilObjSystemFolder->_getHeaderTitle();
		$tpl->setVariable("TXT_HEADER_TITLE", $header_top_title);

		return $tpl->get();
	}


	protected function getHeaderURL() {
		include_once './Services/User/classes/class.ilUserUtil.php';
		$url = ilUserUtil::getStartingPointAsUrl();

		if (!$url) {
			$url = "./goto.php?target=root_1";
		}

		return $url;
	}
}

?>
