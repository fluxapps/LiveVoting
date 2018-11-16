<?php

namespace srag\CustomInputGUIs\LiveVoting\PropertyFormGUI;

use ilCheckboxInputGUI;
use ilDateTimeInputGUI;
use ilFormPropertyGUI;
use ilFormSectionHeaderGUI;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Exception\PropertyFormGUIException;
use srag\DIC\LiveVoting\Exception\DICException;

/**
 * Class BasePropertyFormGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\PropertyFormGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class PropertyFormGUI extends BasePropertyFormGUI {

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
	 * PropertyFormGUI constructor
	 *
	 * @param object $parent
	 */
	public function __construct($parent) {
		parent::__construct($parent);
	}


	/**
	 * @param array                               $fields
	 * @param ilPropertyFormGUI|ilFormPropertyGUI $parent_item
	 *
	 * @throws PropertyFormGUIException $fields needs to be an array!
	 * @throws PropertyFormGUIException Class $class not exists!
	 * @throws PropertyFormGUIException $item muss be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!
	 * @throws PropertyFormGUIException $options needs to be an array!
	 */
	private final function getFields(array $fields, $parent_item)/*: void*/ {
		if (!is_array($fields)) {
			throw new PropertyFormGUIException("\$fields needs to be an array!");
		}

		foreach ($fields as $key => $field) {
			if (!is_array($field)) {
				throw new PropertyFormGUIException("\$fields needs to be an array!");
			}
			if (!class_exists($field[self::PROPERTY_CLASS])) {
				throw new PropertyFormGUIException("Class " . $field[self::PROPERTY_CLASS] . " not exists!");
			}

			$item = $this->getItem($key, $field, $parent_item);

			if (!($item instanceof ilFormPropertyGUI || $item instanceof ilFormSectionHeaderGUI || $item instanceof ilRadioOption)) {
				throw new PropertyFormGUIException("\$item muss be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!");
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
	 * @param string                              $key
	 * @param array                               $field
	 * @param ilPropertyFormGUI|ilFormPropertyGUI $parent_item
	 *
	 * @return ilFormPropertyGUI|ilFormSectionHeaderGUI|ilRadioOption
	 */
	private final function getItem($key, array $field, $parent_item) {
		/**
		 * @var ilFormPropertyGUI|ilFormSectionHeaderGUI|ilRadioOption $item
		 */
		$item = new $field[self::PROPERTY_CLASS]();

		if ($item instanceof ilFormSectionHeaderGUI) {
			$item->setTitle($this->txt($key));
		} else {
			if ($item instanceof ilRadioOption) {
				$item->setTitle($this->txt($parent_item->getPostVar() . "_" . $key));

				$item->setValue($key);
			} else {
				$item->setTitle($this->txt($key));

				$item->setPostVar($key);
			}
		}

		$item->setInfo($this->txt($key . "_info", ""));

		$this->setPropertiesToItem($item, $field);

		if ($item instanceof ilFormPropertyGUI) {
			$value = $this->getValue($key);

			$this->setValueToItem($item, $value);
		}

		$this->items_cache[$key] = $item;

		return $item;
	}


	/**
	 * @param array $fields
	 */
	private final function getValueFromItems(array $fields)/*: void*/ {
		foreach ($fields as $key => $field) {
			$item = $this->items_cache[$key];

			if ($item instanceof ilFormPropertyGUI) {
				$value = $this->getValueFromItem($item);

				$this->setValue($key, $value);
			}

			if (is_array($field[self::PROPERTY_SUBITEMS])) {
				$this->getValueFromItems($field[self::PROPERTY_SUBITEMS]);
			}
		}
	}


	/**
	 * @param ilFormPropertyGUI|ilFormSectionHeaderGUI|ilRadioOption $item
	 *
	 * @return mixed
	 */
	private final function getValueFromItem($item) {
		if ($item instanceof ilCheckboxInputGUI) {
			return boolval($item->getChecked());
		} else {
			if ($item instanceof ilDateTimeInputGUI) {
				return $item->getDate();
			} else {
				if ($item->getMulti()) {
					return $item->getMultiValues();
				} else {
					return $item->getValue();
				}
			}
		}
	}


	/**
	 * @inheritdoc
	 */
	protected final function initItems()/*: void*/ {
		$this->initFields();

		$this->getFields($this->fields, $this);
	}


	/**
	 * @param ilFormPropertyGUI|ilFormSectionHeaderGUI|ilRadioOption $item
	 * @param array                                                  $properties
	 */
	private final function setPropertiesToItem($item, array $properties)/*: void*/ {
		foreach ($properties as $property_key => $property_value) {
			$property = "";

			switch ($property_key) {
				case self::PROPERTY_DISABLED:
					$property = "setDisabled";
					break;

				case self::PROPERTY_MULTI:
					$property = "setMulti";
					break;

				case self::PROPERTY_OPTIONS:
					$property = "setOptions";
					$property_value = [ $property_value ];
					break;

				case self::PROPERTY_REQUIRED:
					$property = "setRequired";
					break;

				case self::PROPERTY_CLASS:
				case self::PROPERTY_SUBITEMS:
					break;

				default:
					$property = $property_key;
					break;
			}

			if (!empty($property)) {
				if (!is_array($property_value)) {
					$property_value = [ $property_value ];
				}

				call_user_func_array([ $item, $property ], $property_value);
			}
		}
	}


	/**
	 * @param ilFormPropertyGUI|ilFormSectionHeaderGUI|ilRadioOption $item
	 * @param mixed                                                  $value
	 */
	private final function setValueToItem($item, $value)/*: void*/ {
		if ($item instanceof ilCheckboxInputGUI) {
			$item->setChecked($value);
		} else {
			if ($item instanceof ilDateTimeInputGUI) {
				$item->setDate($value);
			} else {
				if (!$item instanceof ilRadioOption) {
					$item->setValue($value);
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
	protected final function txt(/*string*/
		$key,/*?string*/
		$default = NULL)/*: string*/ {
		if ($default !== NULL) {
			try {
				return self::plugin()->translate($key, static::LANG_MODULE, [], true, "", $default);
			} catch (DICException $ex) {
				return $default;
			}
		} else {
			try {
				return self::plugin()->translate($key, static::LANG_MODULE);
			} catch (DICException $ex) {
				return "";
			}
		}
	}


	/**
	 * @inheritdoc
	 */
	public function updateForm()/*: void*/ {
		$this->getValueFromItems($this->fields);
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
	protected abstract function initFields()/*: void*/
	;


	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	protected abstract function setValue(/*string*/
		$key, $value)/*: void*/
	;
}
