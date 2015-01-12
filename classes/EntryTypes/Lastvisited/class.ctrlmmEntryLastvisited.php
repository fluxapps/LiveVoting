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
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryTypes/Dropdown/class.ctrlmmEntryDropdown.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryTypes/Link/class.ctrlmmEntryLink.php');
require_once('./Services/Navigation/classes/class.ilNavigationHistory.php');
require_once('./Services/Object/classes/class.ilObject2.php');

/**
 * Application class for ctrlmmEntryLastvisited Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryLastvisited extends ctrlmmEntryDropdown {

	/**
	 * @var bool
	 */
	protected $use_image = true;
	/**
	 * @var string
	 */
	protected $target = '';
	/**
	 * @var bool
	 */
	protected $show_icons = true;
	/**
	 * @var int
	 */
	//protected $type = ctrlmmMenu::TYPE_LASTVISITED;

    public function __construct($primary_key = 0) {
		parent::__construct($primary_key);

        $this->setType(ctrlmmMenu::TYPE_LASTVISITED);

		if($primary_key != 0) {
			$this->setHistory();
		}
    }

	protected function setHistory() {
		$entries = array();
		$hist = new ilNavigationHistory();
		foreach ($hist->getItems() as $v) {
			$e = new ctrlmmEntryLink();
			$e->setLink($v['link']);
			if ($this->getShowIcons()) {
				$icon = ilUtil::img(ilObject::_getIcon(ilObject2::_lookupObjId($v['ref_id']), "tiny"));
				$e->setTitle($icon . ' ' . $v['title']);
			} else {
				$e->setTitle($v['title']);
			}
			$e->setTarget($this->getTarget());
			$entries[] = $e;
		}
		$this->setEntries($entries);
	}

	/**
	 * @param boolean $show_icons
	 */
	public function setShowIcons($show_icons) {
		$this->show_icons = $show_icons;
	}


	/**
	 * @return boolean
	 */
	public function getShowIcons() {
		return $this->show_icons;
	}
}