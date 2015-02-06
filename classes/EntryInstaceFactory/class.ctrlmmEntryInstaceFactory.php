<?php
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ctrlmmMenu.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');

/**
 * Class ctrlmmEntryInstaceFactory
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ctrlmmEntryInstaceFactory {

	/**
	 * @var int
	 */
	protected $type_id = ctrlmmMenu::TYPE_LINK;
	/**
	 * @var int
	 */
	protected $entry_id = 0;
	/**
	 * @var string
	 */
	protected $class_name = '';
	/**
	 * @var array
	 */
	protected static $type_id_cache = array();
	/**
	 * @var array
	 */
	protected static $childs_cache = array();


	/**
	 * @param     $type_id
	 * @param int $entry_id
	 */
	protected function __construct($type_id, $entry_id = 0) {
		ctrlmmMenu::includeAllTypes();
		$this->setEntryId($entry_id);
		$this->setTypeId($type_id);
		$this->setClassName('ctrlmmEntry' . self::getClassAppendForValue($type_id));
	}


	/**
	 * @param $id
	 *
	 * @return ctrlmmEntry[]
	 */
	public static function getAllChildsForId($id) {
		if (!isset(self::$childs_cache[$id])) {
			$children = array();
			$sets = ctrlmmEntry::where(array( 'parent' => $id ))->orderBy('position', 'ASC');
			foreach ($sets->get() as $set) {
				$instance = self::getInstanceByEntryId($set->getId())->getObject();
				$children[] = $instance;
			}
			if (count($children) === 0 AND $id === 0) {
				$children[] = self::createAdminEntry();
			}

			$children = array_merge($children, self::getPluginEntries($id));

			self::$childs_cache[$id] = $children;
		}

		return self::$childs_cache[$id];
	}


	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getAllChildsForIdAsArray($id = 0) {
		$return = array();
		foreach (ctrlmmEntryInstaceFactory::getAllChildsForId($id) as $child) {
			$return[] = (array)$child;
		}

		return $return;
	}


	/**
	 * @param bool $as_array
	 *
	 * @return ctrlmmEntry[]
	 */
	public static function getAll($as_array = false) {
		ctrlmmMenu::includeAllTypes();

		$childs = array();
		$sets = ctrlmmEntry::getArray();
		foreach ($sets as $set) {
			$type = 'ctrlmmEntry' . self::getClassAppendForValue($set->type);

			if ($as_array) {
				$childs[] = (array)new $type($set->id);
			} else {
				$childs[] = new $type($set->id);
			}
		}

		return $childs;
	}


	public static function createAdminEntry() {
		$admin = self::getInstanceByTypeId(ctrlmmMenu::TYPE_ADMIN)->getObject();

		$lngs = array();
		foreach (ctrlmmEntry::getAllLanguageIds() as $lng) {
			$lngs[$lng] = $admin->getTitle();
		}
		$admin->setTranslations($lngs);
		$admin->setPosition(3);
		$admin->create();

		return $admin;
	}


	/**
	 * @param int $id
	 *
	 * @return array
	 */
	protected static function getPluginEntries($id = 0) {
		$childs = array();
		foreach (ilPluginAdmin::$active_plugins as $slot) {
			foreach ($slot as $hook) {
				foreach ($hook as $pls) {
					foreach ($pls as $pl) {
						$plugin_class = 'il' . $pl . 'Plugin';
						if (method_exists($plugin_class, 'getMenuEntries')) {
							$menuEntries = $plugin_class::getMenuEntries($id);
							if (is_array($menuEntries)) {
								$childs = array_merge($childs, $menuEntries);
							}
						}
					}
				}
			}
		}

		return $childs;
	}


	/**
	 * @param $type_id
	 *
	 * @return \ctrlmmEntryInstaceFactory
	 */
	public static function getInstanceByTypeId($type_id) {
		return new self($type_id);
	}


	/**
	 * @param $entry_id
	 *
	 * @return \ctrlmmEntryInstaceFactory
	 */
	public static function getInstanceByEntryId($entry_id) {
		if (!isset(self::$type_id_cache[$entry_id])) {
			$obj = ctrlmmEntry::find($entry_id);
			if ($obj) {
				self::$type_id_cache[$entry_id] = $obj->getType();
			}
		}

		if (ctrlmm::isGlobalCacheActive()) {
			require_once('./Services/GlobalCache/classes/class.ilGlobalCache.php');
			$ilGlobalCache = ilGlobalCache::getInstance(ilGlobalCache::COMP_ILCTRL);
			if ($ilGlobalCache->isActive()) {
				$entry = $ilGlobalCache->get('ctrlmm_e_' . $entry_id);
				if (!$entry instanceof ctrlmmEntryInstaceFactory) {
					$entry = new self(self::$type_id_cache[$entry_id], $entry_id);
					$ilGlobalCache->set('ctrlmm_e_' . $entry_id, $entry, 120);
				}

				return $entry;
			}
		}

		return new self(self::$type_id_cache[$entry_id], $entry_id);
	}


	/**
	 * @var ctrlmmEntryCtrlGUI
	 */
	protected $object;
	/**
	 * @var ctrlmmEntryCtrl
	 */
	protected $object_gui;


	/**
	 * @return ctrlmmEntryCtrl
	 *
	 * TODO FSX add caching
	 */
	public function getObject() {
		if (!isset($this->object)) {
			/**
			 * @var $entry_class  ctrlmmEntryCtrl
			 */
			$entry_class = $this->getClassName();
			$this->object = $entry_class::find($this->getEntryId());
		}

		return $this->object;
	}


	/**
	 * @param null $parent_gui
	 *
	 * @return ctrlmmEntryCtrlGUI
	 */
	public function getGUIObject($parent_gui = NULL) {

		/**
		 * @var $entry_class  ctrlmmEntryCtrl
		 * @var $gui_class    ctrlmmEntryCtrlGUI
		 * @var $gui_object   ctrlmmEntryCtrlGUI
		 */
		$gui_class = $this->getGUIObjectClass();

		$gui_object = new $gui_class($this->getObject(), $parent_gui);

		return $gui_object;
	}


	/**
	 * @param $parent_gui
	 *
	 * @return ctrlmmEntryCtrlFormGUI
	 */
	public function getFormObject($parent_gui) {
		/**
		 * @var $entry_class  ctrlmmEntryCtrl
		 * @var $gui_class    ctrlmmEntryCtrlFormGUI
		 * @var $gui_object   ctrlmmEntryCtrlFormGUI
		 */
		$entry_class = $this->getClassName();
		$gui_class = $entry_class . 'FormGUI';

		$gui_object = new $gui_class($parent_gui, $this->getObject());

		return $gui_object;
	}


	/**
	 * @param string $class_name
	 */
	public function setClassName($class_name) {
		$this->class_name = $class_name;
	}


	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->class_name;
	}


	/**
	 * @param int $entry_id
	 */
	public function setEntryId($entry_id) {
		$this->entry_id = $entry_id;
	}


	/**
	 * @return int
	 */
	public function getEntryId() {
		return $this->entry_id;
	}


	/**
	 * @param int $type_id
	 */
	public function setTypeId($type_id) {
		$this->type_id = $type_id;
	}


	/**
	 * @return int
	 */
	public function getTypeId() {
		return $this->type_id;
	}


	/**
	 * @param $id
	 *
	 * @return string
	 */
	public static function getClassConstantForId($id) {
		$constants = array_flip(ctrlmmMenu::getAllTypeConstants());

		return $constants[$id];
	}


	/**
	 * @param $id
	 *
	 * @return string
	 */
	public static function getClassAppendForValue($id) {
		return ucfirst(strtolower(str_ireplace('TYPE_', '', self::getClassConstantForId($id))));
	}


	/**
	 * @return string
	 */
	public function getGUIObjectClass() {
		$entry_class = $this->getClassName();
		$gui_class = $entry_class . 'GUI';

		return $gui_class;
	}
}

?>
