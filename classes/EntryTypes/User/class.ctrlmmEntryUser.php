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
 * Application class for ctrlmmEntryCtrl Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryUser extends ctrlmmEntry {

	/**
	 * @var bool
	 */
	protected $logout = false;
	/**
	 * @var bool
	 */
	protected $personal_desktop = true;
	/**
	 * @var bool
	 */
	protected $user_image = true;
	/**
	 * @var int
	 */
	protected $type = ctrlmmMenu::TYPE_USER;


	/**
	 * @return string
	 */
	public function getLink() {
		return '';
	}


	/**
	 * @param boolean $user_image
	 */
	public function setUserImage($user_image) {
		$this->user_image = $user_image;
	}


	/**
	 * @return boolean
	 */
	public function getUserImage() {
		return $this->user_image;
	}


	/**
	 * @param boolean $personal_desktop
	 */
	public function setPersonalDesktop($personal_desktop) {
		$this->personal_desktop = $personal_desktop;
	}


	/**
	 * @return boolean
	 */
	public function getPersonalDesktop() {
		return $this->personal_desktop;
	}


	/**
	 * @param boolean $logout
	 */
	public function setLogout($logout) {
		$this->logout = $logout;
	}


	/**
	 * @return boolean
	 */
	public function getLogout() {
		return $this->logout;
	}
}