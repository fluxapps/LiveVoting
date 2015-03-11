<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmData.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmmTranslation.php');
require_once('./Services/Language/classes/class.ilLanguage.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ctrlmmMenu.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryInstaceFactory/class.ctrlmmEntryInstaceFactory.php');
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

/**
 * Application class for ctrlmmEntry Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntry extends ActiveRecord {

	const TABLE_NAME = 'ui_uihk_ctrlmm_e';
	/**
	 * @var array
	 */
	protected static $restricted_types = array( ctrlmmMenu::TYPE_ADMIN );
	/**
	 * @var array
	 */
	protected static $childs_cache = array();
	/**
	 * @var array
	 */
	protected static $active_cache = array();
	/**
	 * @var array
	 */
	protected static $permission_cache = array();
	/**
	 * @var array
	 */
	protected $forbidden_children = array();
	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_sequence   true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	public $id;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $position = 99;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $type = ctrlmmMenu::TYPE_LINK;
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    255
	 */
	protected $link = '';
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    255
	 */
	protected $permission = '';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $permission_type = ctrlmmMenu::PERM_NONE;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $parent = 0;
	/**
	 * @var string
	 */
	protected $target = '_top';
	/**
	 * @var array
	 */
	protected $translations = array();
	/**
	 * @var bool
	 */
	protected $restricted = false;
	/**
	 * @var bool
	 */
	protected $plugin = false;
	/**
	 * @var string
	 */
	protected $icon = NULL;
	/**
	 * @var string
	 */
	protected $title = '';


	/**
	 * @return string
	 * @description Return the Name of your Database Table
	 * @deprecated
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	public function __construct($primary_key = 0) {
		parent::__construct($primary_key);

		if (isset($primary_key)) {
			foreach (ctrlmmData::getDataForEntry($this->getId()) as $k => $v) {
				if (self::isAdditionalField(get_class($this), $k)) {
					$this->{$k} = $v;
				}
			}

			$this->setTitle(ctrlmmTranslation::_getTitleForEntryId($this->getId()));
			$this->setTranslations(ctrlmmTranslation::_getAllTranslationsAsArray($this->getId()));
		}
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @param $obj_name
	 * @param $field_name
	 *
	 * @return bool
	 */
	public static function isAdditionalField($obj_name, $field_name) {
		$properties = array();
		$reflection = new ReflectionClass($obj_name);
		foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED) as $property) {
			$properties[] = $property->getName();
		}

		$array_diff = array_diff($properties, array(
			'ctrl',
			'permission',
			'permission_type',
			'link',
			'id',
			'type',
			'title',
			'position',
			'parent',
			'entries',
			'translations',
			'plugin',
			'arConnector',
			'arFieldList',
			'ar_safe_read',
			'connector_container_name',
			'restricted_types',
			'restricted',
			'childs_cache',
			'active_cache',
			'permission_cache',
//			'target',
			'icon',
			'is_new',
			'forbidden_children',
		));

		return in_array($field_name, $array_diff);
	}


	/**
	 * @param $type_id
	 *
	 * @return bool
	 */
	public static function isSecondInstanceAllowed($type_id) {
		if (!in_array($type_id, self::$restricted_types)) {
			return true;
		} else {
			if ($type_id == ctrlmmMenu::TYPE_CTRL) {
				return false;
			}

			return self::entriesExistForType($type_id) ? false : true;
		}
	}


	/**
	 * @param $type_id
	 *
	 * @return bool
	 */
	public static function entriesExistForType($type_id) {
		$set = self::where(array( 'type' => $type_id ));

		return ($set->count() > 0 ? true : false);
	}


	//
	// Static
	//

	/**
	 * Return all entries for a given command class
	 *
	 * @param $cmdClass
	 *
	 * @return array ctrlmmEntry[]
	 */
	public static function getEntriesByCmdClass($cmdClass) {
		$sets = ctrlmmData::where(array( 'data_value' => '%' . $cmdClass ), 'LIKE');

		$entries = array();
		foreach ($sets->get() as $set) {
			$entries[] = new self($set->getParentId());
		}

		return $entries;
	}


	/**
	 * @param $id
	 *
	 * @deprecated
	 * @return string
	 */
	public static function getPermissionTypeForValue($id) {
		return strtolower(str_ireplace('PERM_', '', ctrlmmEntryInstaceFactory::getClassConstantForId($id)));
	}


	/**
	 * @param $lng
	 *
	 * @return bool
	 */
	public static function isDefaultLanguage($lng) {
		$lngs = new ilLanguage('en');

		return $lngs->getDefaultLanguage() == $lng ? true : false;
	}


	/**
	 * @param $type_id
	 *
	 * @return bool
	 */
	public function isChildAllowed($type_id) {
		return !in_array($type_id, $this->forbidden_children);
	}


	/**
	 * @return null
	 */
	protected function getError() {
		return NULL;
	}


	/**
	 * @return string
	 */
	public function getTitleInAdministration() {
		return $this->getTitle() . ($this->getError() ? ' (' . $this->getError() . ')' : '');
	}


	public function replacePlaceholders() {
		global $ilUser;
		$replacements = array(
			'[firstname]' => "<span class='headerFirstname'>" . $ilUser->getFirstname() . "</span>",
			'[lastname]' => "<span class='headerLastname'>" . $ilUser->getLastname() . "</span>",
			'[login]' => "<span class='headerLogin' > " . $ilUser->getLogin() . "</span>",
			'[email]' => "<span class='headerEmail'>" . $ilUser->getEmail() . "</span>",
			'[picture]' => "<img class='headerImage' src='" . $ilUser->getPersonalPicturePath('xxsmall') . "' />",
		);

		$this->setTitle(strtr($this->getTitle(), $replacements));
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return array
	 */
	public static function getAllLanguageIds() {
		$lngs = new ilLanguage('en');

		return $lngs->getInstalledLanguages();
	}


	public function create() {
		if ($this->getParent() > 0) {
			$entry = ctrlmmEntryInstaceFactory::getInstanceByEntryId($this->getParent())->getObject();
			if (!$entry->isChildAllowed($this->getType())) {
				ilUtil::sendFailure('Wrong Child-Type');

				return false;
			}
		}
		if ($this->getId() != 0) {
			$this->update();
		} else {
			parent::create();

			$this->writeAdditionalData();
			$this->writeTranslations();
		}
	}


	/**
	 * @return int
	 */
	public function getParent() {
		return $this->parent;
	}


	/**
	 * @param int $parent
	 */
	public function setParent($parent) {
		$this->parent = $parent;
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	public function update() {
		parent::update();

		$this->writeAdditionalData();
		$this->writeTranslations();
	}


	/**
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}


	/**
	 * @param string $link
	 */
	public function setLink($link) {
		$this->link = $link;
	}


	/**
	 * @return string $permission (comma separated id's)
	 */
	public function getPermission() {
		return $this->permission;
	}


	/**
	 * @param string $permission (comma separated id's)
	 */
	public function setPermission($permission) {
		$this->permission = $permission;
	}


	/**
	 * @return int
	 */
	public function getPermissionType() {
		return $this->permission_type;
	}


	/**
	 * @param int $permission_type
	 */
	public function setPermissionType($permission_type) {
		$this->permission_type = $permission_type;
	}


	/**
	 * @return int
	 */
	public function getPosition() {
		return $this->position;
	}


	/**
	 * @param int $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}


	protected function writeAdditionalData() {
		foreach (self::getAdditionalFieldsAsArray($this) as $k => $v) {
			$data = ctrlmmData::_getInstanceForDataKey($this->getId(), $k);
			$data->setDataValue($v);
			$data->store();
		}
	}


	/**
	 * @param $obj
	 *
	 * @return array
	 */
	public static function getAdditionalFieldsAsArray($obj) {
		$return = array();
		$reflection = new ReflectionClass($obj);
		foreach ($reflection->getProperties() as $property) {
			$k = $property->getName();
			if (self::isAdditionalField(get_class($obj), $k)) {
				$return[$k] = $obj->{$k};
			}
		}

		return $return;
	}


	protected function writeTranslations() {
		foreach ($this->getTranslations() as $k => $v) {
			$trans = ctrlmmTranslation::_getInstanceForLanguageKey($this->getId(), $k);
			$trans->setTitle($v);
			$trans->store();
		}
	}


	/**
	 * @return array
	 */
	public function getTranslations() {
		return $this->translations;
	}


	/**
	 * @param array $translations
	 */
	public function setTranslations($translations) {
		$this->translations = $translations;
	}


	/**
	 * @return int
	 */
	public function delete() {
		$deleted_id = $this->getId();
		if ($this->getType() == ctrlmmMenu::TYPE_DROPDOWN) {
			/**
			 * @var $entry ctrlmmEntry
			 */
			foreach (ctrlmmEntryInstaceFactory::getAllChildsForId($this->getId()) as $entry) {
				$entry->delete();
			}
		}

		ctrlmmTranslation::_deleteAllInstancesForEntryId($deleted_id);
		ctrlmmData::_deleteAllInstancesForParentId($deleted_id);

		parent::delete();
	}


	/**
	 * @return bool
	 */
	public function checkPermission() {
		if (!$this->isPermissionCached()) {
			$this->setCachedPermission(false);
			global $ilAccess, $ilUser, $rbacreview;
			switch ($this->getPermissionType()) {
				case ctrlmmMenu::PERM_ROLE:
					foreach ((array)json_decode($this->getPermission()) as $pid) {
						if (in_array($pid, $rbacreview->assignedRoles($ilUser->getId()))) {
							$this->setCachedPermission(true);
						}
					}
					break;
				case ctrlmmMenu::PERM_ROLE_EXEPTION:
					$perm = (array)json_decode($this->getPermission());
					$assignedRoles = $rbacreview->assignedRoles($ilUser->getId());

					$state = count(array_intersect($perm, $assignedRoles)) == 0;

					$this->setCachedPermission($state);

					break;
				case ctrlmmMenu::PERM_REF_READ:
					if ($ilAccess->checkAccess('read', '', $this->getPermission())) {
						$this->setCachedPermission(true);
					}
					break;
				case ctrlmmMenu::PERM_REF_WRITE:
					if ($ilAccess->checkAccess('write', '', $this->getPermission())) {
						$this->setCachedPermission(true);
					}
					break;
				case ctrlmmMenu::PERM_USERID:
					$state = in_array($ilUser->getId(), json_decode($this->getPermission()));
					$this->setCachedPermission($state);

					break;
				case ctrlmmMenu::PERM_NONE:
				case NULL;
					$this->setCachedPermission(true);
					break;
				default:
					$this->setCachedPermission(false);
					break;
			}
		}

		return $this->getCachedPermission();
	}


	/**
	 * @return bool
	 */
	protected function isPermissionCached() {
		return isset(self::$permission_cache[$this->getId()]);
	}


	/**
	 * @param $active
	 */
	protected function setCachedPermission($active) {
		self::$permission_cache[$this->getId()] = $active;
	}


	/**
	 * @return bool
	 */
	protected function getCachedPermission() {
		return self::$permission_cache[$this->getId()];
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		return false;
	}


	/**
	 * @return string
	 */
	public function getRawTitle() {
		return $this->title;
	}


	/**
	 * @return string
	 */
	public function getTarget() {
		return $this->target;
	}


	/**
	 * @param string $target
	 */
	public function setTarget($target) {
		$this->target = $target;
	}


	/**
	 * @return boolean
	 */
	public function getPlugin() {
		return $this->plugin;
	}


	/**
	 * @param boolean $plugin
	 */
	public function setPlugin($plugin) {
		$this->plugin = $plugin;
	}

	//
	// Helper
	//

	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}


	/**
	 * @param string $icon
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
	}


	public static function addRestrictedType($type) {
		self::$restricted_types[] = $type;
	}
}