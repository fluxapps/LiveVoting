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
 * Application class for ctrlmmEntryRepository Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryRepository extends ctrlmmEntry {

	/**
	 * @var int
	 */
	protected $max_history_items = 10;
	/**
	 * @var bool
	 */
	protected $restricted = true;
	/**
	 * @var int
	 */
	protected $type = ctrlmmMenu::TYPE_REPOSITORY;


	/**
	 * @param int $max_history_items
	 */
	public function setMaxHistoryItems($max_history_items) {
		$this->max_history_items = $max_history_items;
	}


	/**
	 * @return int
	 */
	public function getMaxHistoryItems() {
		return $this->max_history_items;
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		global $ilMainMenu;

		return $this->hasNoOtherActive() AND ($ilMainMenu->active == 'repository' OR $ilMainMenu->active == NULL);
	}


	/**
	 * @return bool
	 */
	protected function hasNoOtherActive() {
		return true;
		$active = 0;
		foreach (self::getAllChildsForId($this->getParent()) as $entry) {
			if ($entry->getId() == $this->getId()) {
				continue;
			}

			if ($entry->isActive()) {
				$active ++;
			}
		}

		return $active == 0;
	}


	/**
	 * @return bool
	 */
	public function checkPermission() {
		global $ilAccess;

		return parent::checkPermission() AND $ilAccess->checkAccess('visible', '', ROOT_FOLDER_ID);
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		if (parent::getTitle()) {
			return parent::getTitle();
		} else {
			global $lng;

			return $lng->txt('repository');
		}
	}
}