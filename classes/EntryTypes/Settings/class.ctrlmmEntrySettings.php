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
class ctrlmmEntrySettings extends ctrlmmEntry {

	/**
	 * @var int
	 */
	//protected $type = ctrlmmMenu::TYPE_SETTINGS;
	/**
	 * @var bool
	 */
	protected $show_icon = true;
	/**
	 * @var bool
	 */
	protected $show_title = false;


	/**
	 * @param int $primary_key
	 */
	public function __construct($primary_key = 0) {
		$this->setType(ctrlmmMenu::TYPE_SETTINGS);

		parent::__construct($primary_key);
	}


	/**
	 * @return string
	 */
	public function getIcon() {
		if ($this->getShowIcon()) {
			if (ctrlmm::is50()) {
				return ilUtil::img(ilUtil::getImagePath('icon_adm.svg'), 16, 16);
			} else {
				return ilUtil::img(ilUtil::getImagePath('icon_extt_s.png'));
			}
		}

		return NULL;
	}


	/**
	 * @return string
	 */
	public function getLink() {
		return '';
	}


	/**
	 * @param boolean $show_icon
	 */
	public function setShowIcon($show_icon) {
		$this->show_icon = $show_icon;
	}


	/**
	 * @return boolean
	 */
	public function getShowIcon() {
		return $this->show_icon;
	}


	/**
	 * @param boolean $show_title
	 */
	public function setShowTitle($show_title) {
		$this->show_title = $show_title;
	}


	/**
	 * @return boolean
	 */
	public function getShowTitle() {
		return $this->show_title;
	}
}