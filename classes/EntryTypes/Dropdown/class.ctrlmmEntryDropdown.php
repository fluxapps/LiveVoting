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
 * Application class for ctrlmmEntryDropdown Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryDropdown extends ctrlmmEntry {

	/**
	 * @var ctrlmmEntry[]
	 */
	protected $entries = array();
	/**
	 * @var bool
	 */
	protected $use_image = false;
	/**
	 * @var int
	 */
	protected $type = ctrlmmMenu::TYPE_DROPDOWN;


	public function read() {
		parent::read();
		$this->setEntries(ctrlmmEntry::getAllChildsForId($this->getId()));
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		foreach ($this->getEntries() as $entry) {
			if ($entry->isActive()) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @param array $entries
	 */
	public function setEntries($entries) {
		$this->entries = $entries;
	}


	/**
	 * @return ctrlmmEntry[]
	 */
	public function getEntries() {
		return $this->entries;
	}


	/**
	 * @param boolean $use_image
	 */
	public function setUseImage($use_image) {
		$this->use_image = $use_image;
	}


	/**
	 * @return boolean
	 */
	public function getUseImage() {
		return $this->use_image;
	}
}