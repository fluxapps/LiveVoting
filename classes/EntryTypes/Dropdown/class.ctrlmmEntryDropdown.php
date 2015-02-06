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
	 * @var string
	 */
	private $list_id = '';
	/**
	 * @var bool
	 */
	protected $use_user_image = false;
	/**
	 * @var
	 */
	protected $forbidden_children = array(
		ctrlmmMenu::TYPE_ADMIN,
		ctrlmmMenu::TYPE_AUTH,
		ctrlmmMenu::TYPE_DESKTOP,
		ctrlmmMenu::TYPE_DROPDOWN,
		ctrlmmMenu::TYPE_LASTVISITED,
		ctrlmmMenu::TYPE_REPOSITORY,
		ctrlmmMenu::TYPE_SEARCH,
		ctrlmmMenu::TYPE_SETTINGS,
		ctrlmmMenu::TYPE_STATUSBOX,
		ctrlmmMenu::TYPE_STATUSBOX,
	);


	/**
	 * @param int $primary_key
	 */
	public function __construct($primary_key = 0) {
		parent::__construct($primary_key);

		$this->setType(ctrlmmMenu::TYPE_DROPDOWN);

		if ($primary_key != 0) {
			$this->setEntries(ctrlmmEntryInstaceFactory::getAllChildsForId($this->getId()));
		}
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


	public function getTitle() {
		if ($this->getUseUserImage()) {
			global $ilias;

			$user_img_src = $ilias->account->getPersonalPicturePath("small", true);
			$user_img_alt = $ilias->account->getFullname();

			return '<img src="' . $user_img_src . '" alt="' . $user_img_alt . '" class="dropdown_image" />';
		} else {
			return $this->title;
		}
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


	public function getUseUserImage() {
		return $this->use_user_image;
	}


	public function setUseUserImage($useUserImage) {
		$this->use_user_image = $useUserImage;
		if ($useUserImage) {
			$this->setListId('userlog');
		}
	}


	/**
	 * @return string
	 */
	public function getListId() {
		return $this->list_id;
	}


	/**
	 * @param string $override_id
	 */
	public function setListId($list_id) {
		$this->list_id = $list_id;
	}
}