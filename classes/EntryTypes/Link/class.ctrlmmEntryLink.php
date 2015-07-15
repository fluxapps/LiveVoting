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
 * @version        2.0.02
 */
class ctrlmmEntryLink extends ctrlmmEntry {
	const PARAM_NAME = 'param_name';
	const PARAM_VALUE = 'param_value';

	/**
	 * @return bool
	 */
	public function isActive() {
		return false;
	}


	/**
	 * @var bool
	 */
	protected $target = '_blank';

	/**
	 * @var array
	 */
	protected $get_params = array();


	/**
	 * @param int $primary_key
	 */
	public function __construct($primary_key = 0) {
		$this->setType(ctrlmmMenu::TYPE_LINK);

		parent::__construct($primary_key);
	}

	public function getLink() {
		$param_string = "";

		if(is_array($this->getGetParams())) {
			foreach($this->getGetParams() as $entry) {
				if($entry[self::PARAM_NAME] != "") {
					$param_string .= '&'.$entry[self::PARAM_NAME].'='.ctrlmmUserDataReplacer::parse($entry[self::PARAM_VALUE]);
				}
			}
		}

		return $this->link.$param_string;
	}


	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->link;
	}


	/**
	 * @param $value string
	 */
	public function setUrl($value) {
		$this->link = $value;
	}


	/**
	 * @return array
	 */
	public function getGetParams() {
		return $this->get_params;
	}


	/**
	 * @param array $get_params
	 */
	public function setGetParams($get_params) {
		$this->get_params = $get_params;
	}
}