<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');

/**
 * Application class for ctrlmmEntryDesktop Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryDesktop extends ctrlmmEntry {

	/**
	 * @var bool
	 */
	protected $restricted = true;
	/**
	 * @var bool
	 */
	protected $show_logout = true;
	/**
	 * @var int
	 */
	protected $type = ctrlmmMenu::TYPE_DESKTOP;


	/**
	 * @param boolean $show_logout
	 */
	public function setShowLogout($show_logout) {
		$this->show_logout = $show_logout;
	}


	/**
	 * @return boolean
	 */
	public function getShowLogout() {
		return $this->show_logout;
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		global $ilMainMenu;

		return $ilMainMenu->active == 'desktop';
	}


	/**
	 * @return bool
	 */
	public function checkPermission() {
		return parent::checkPermission() AND $_SESSION['AccountId'] != ANONYMOUS_USER_ID;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		if (parent::getTitle()) {
			return parent::getTitle();
		} else {
			global $lng;

			return $lng->txt('personal_desktop');
		}
	}
}