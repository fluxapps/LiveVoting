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
 * ctrlmmEntryStatusbox
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryStatusbox extends ctrlmmEntry {

	/**
	 * @var bool
	 */
	protected $restricted = true;


	/**
	 * @return bool
	 */
	public function isActive() {
		return false;
	}


	/**
	 * @param int $primary_key
	 */
	public function __construct($primary_key = 0) {
		$this->setType(ctrlmmMenu::TYPE_STATUSBOX);

		parent::__construct($primary_key);
	}


	/**
	 * @return string
	 */
	public function getLink() {
		return 'ilias.php?baseClass=ilMailGUI';
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return 'Statusbox (ID ' . $this->getId() . ')';
	}


	/**
	 * @return int
	 */
	public function getNewMailCount() {
		global $ilUser;

		return ilMailGlobalServices::getNumberOfNewMailsByUserId($ilUser->getId());
	}
}