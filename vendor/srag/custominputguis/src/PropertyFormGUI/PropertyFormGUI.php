<?php

namespace srag\CustomInputGUIs\LiveVoting\PropertyFormGUI;

use ilFormPropertyGUI;
use ilFormSectionHeaderGUI;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Exception\PropertyFormGUIException;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Items\Items;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class PropertyFormGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\PropertyFormGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class PropertyFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	/**
	 * @var string
	 */
	const PROPERTY_CLASS = "class";
	/**
	 * @var string
	 */
	const PROPERTY_DISABLED = "disabled";
	/**
	 * @var string
	 */
	const PROPERTY_MULTI = "multi";
	/**
	 * @var string
	 */
	const PROPERTY_NOT_ADD = "not_add";
	/**
	 * @var string
	 */
	const PROPERTY_OPTIONS = "options";
	/**
	 * @var string
	 */
	const PROPERTY_REQUIRED = "required";
	/**
	 * @var string
	 */
	const PROPERTY_SUBITEMS = "subitems";
	/**
	 * @var string
	 */
	const PROPERTY_VALUE = "value";
	/**
	 * @var string
	 */
	const LANG_MODULE = "";
	/**
	 * @var array
	 */
	protected $fields = [];
	/**
	 * @var ilFormPropertyGUI[]|ilFormSectionHeaderGUI[]
	 */
	private $items_cache = [];
	/**
	 * @var object
	 */
	protected $parent;


	/**
	 * PropertyFormGUI constructor
	 *
	 * @param object $parent
	 */
	public function __construct($parent) {
		$this->initId();

		parent::__construct();

		$this->parent = $parent;

		$this->initForm();
	}


	/**
	 * @param array                               $fields
	 * @param ilPropertyFormGUI|ilFormPropertyGUI $parent_item
	 *
	 * @throws PropertyFormGUIException $fields needs to be an array!
	 * @throws PropertyFormGUIException Class $class not exists!
	 * @throws PropertyFormGUIException $item must be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!
	 * @throws PropertyFormGUIException $options needs to be an array!
	 */
	private final function getFields(array $fields, $parent_item)/*: void*/ {
		if (!is_array($fields)) {
			throw new PropertyFormGUIException("\$fields needs to be an array!", PropertyFormGUIException::CODE_INVALID_FIELD);
		}

		foreach ($fields as $key => $field) {
			if (!is_array($field)) {
				throw new PropertyFormGUIException("\$fields needs to be an array!", PropertyFormGUIException::CODE_INVALID_FIELD);
			}

			if ($field[self::PROPERTY_NOT_ADD]) {
				continue;
			}

			$item = Items::getItem($key, $field, $parent_item, $this);

			if (!($item instanceof ilFormPropertyGUI || $item instanceof ilFormSectionHeaderGUI || $item instanceof ilRadioOption)) {
				throw new PropertyFormGUIException("\$item must be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!", PropertyFormGUIException::CODE_INVALID_FIELD);
			}

			$this->items_cache[$key] = $item;

			if ($item instanceof ilFormPropertyGUI) {
				if (!isset($field[self::PROPERTY_VALUE])) {
					$value = $this->getValue($key);

					Items::setValueToItem($item, $value);
				}
			}

			if (is_array($field[self::PROPERTY_SUBITEMS])) {
				$this->getFields($field[self::PROPERTY_SUBITEMS], $item);
			}

			if ($parent_item instanceof ilRadioGroupInputGUI) {
				$parent_item->addOption($item);
			} else {
				if ($parent_item instanceof ilPropertyFormGUI) {
					$parent_item->addItem($item);
				} else {
					$parent_item->addSubItem($item);
				}
			}
		}
	}


	/**
	 *
	 */
	private final function initForm()/*: void*/ {
		$this->initAction();

		$this->initCommands();

		$this->initTitle();

		$this->initItems();
	}


	/**
	 *
	 */
	private final function initItems()/*: void*/ {
		$this->initFields();

		$this->getFields($this->fields, $this);
	}


	/**
	 * @return bool
	 */
	protected final function storeFormCheck()/*: bool*/ {
		$this->setValuesByPost();

		if (!$this->checkInput()) {
			return false;
		}

		return true;
	}


	/**
	 * @param array $fields
	 */
	private final function storeFormItems(array $fields)/*: void*/ {
		foreach ($fields as $key => $field) {
			if (isset($this->items_cache[$key])) {
				$item = $this->items_cache[$key];

				if ($item instanceof ilFormPropertyGUI) {
					$value = Items::getValueFromItem($item);

					$this->storeValue($key, $value);
				}

				if (is_array($field[self::PROPERTY_SUBITEMS])) {
					$this->storeFormItems($field[self::PROPERTY_SUBITEMS]);
				}
			}
		}
	}


	/**
	 * @param string      $key
	 * @param string|null $default
	 *
	 * @return string
	 */
	public final function txt(/*string*/
		$key,/*?string*/
		$default = NULL)/*: string*/ {
		if ($default !== NULL) {
			return self::plugin()->translate($key, static::LANG_MODULE, [], true, "", $default);
		} else {
			return self::plugin()->translate($key, static::LANG_MODULE);
		}
	}


	/**
	 * @return bool
	 */
	public function checkInput()/*: bool*/ {
		return parent::checkInput();
	}


	/**
	 *
	 */
	protected function initAction()/*: void*/ {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));
	}


	/**
	 * @return bool
	 */
	public function storeForm()/*: bool*/ {
		if (!$this->storeFormCheck()) {
			return false;
		}

		$this->storeFormItems($this->fields);

		return true;
	}


	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	protected abstract function getValue(/*string*/
		$key);


	/**
	 *
	 */
	protected abstract function initCommands()/*: void*/
	;


	/**
	 *
	 */
	protected abstract function initFields()/*: void*/
	;


	/**
	 *
	 */
	protected abstract function initId()/*: void*/
	;


	/**
	 *
	 */
	protected abstract function initTitle()/*: void*/
	;


	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	protected abstract function storeValue(/*string*/
		$key, $value)/*: void*/
	;
}
