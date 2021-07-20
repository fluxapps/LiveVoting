<?php

namespace srag\CustomInputGUIs\LiveVoting\PropertyFormGUI;

use Closure;
use ilFormPropertyGUI;
use ilFormSectionHeaderGUI;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSubEnabledFormPropertyGUI;
use srag\CustomInputGUIs\LiveVoting\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Exception\PropertyFormGUIException;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\LiveVoting\TabsInputGUI\TabsInputGUI;
use srag\CustomInputGUIs\LiveVoting\TabsInputGUI\TabsInputGUITab;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class PropertyFormGUI
 *
 * @package    srag\CustomInputGUIs\LiveVoting\PropertyFormGUI
 *
 * @deprecated Please use `AbstractFormBuilder`
 */
abstract class PropertyFormGUI extends ilPropertyFormGUI
{

    use DICTrait;

    /**
     * @var string
     *
     * @deprecated
     */
    const LANG_MODULE = "";
    /**
     * @var string
     *
     * @deprecated
     */
    const PROPERTY_CLASS = "class";
    /**
     * @var string
     *
     * @deprecated
     */
    const PROPERTY_DISABLED = "disabled";
    /**
     * @var string
     *
     * @deprecated
     */
    const PROPERTY_MULTI = "multi";
    /**
     * @var string
     *
     * @deprecated
     */
    const PROPERTY_NOT_ADD = "not_add";
    /**
     * @var string
     *
     * @deprecated
     */
    const PROPERTY_OPTIONS = "options";
    /**
     * @var string
     *
     * @deprecated
     */
    const PROPERTY_REQUIRED = "required";
    /**
     * @var string
     *
     * @deprecated
     */
    const PROPERTY_SUBITEMS = "subitems";
    /**
     * @var string
     *
     * @deprecated
     */
    const PROPERTY_VALUE = "value";
    /**
     * @var array
     *
     * @deprecated
     */
    protected $fields = [];
    /**
     * @var object
     *
     * @deprecated
     */
    protected $parent;
    /**
     * @var ilFormPropertyGUI[]|ilFormSectionHeaderGUI[]
     *
     * @deprecated
     */
    private $items_cache = [];


    /**
     * PropertyFormGUI constructor
     *
     * @param object $parent
     *
     * @deprecated
     */
    public function __construct(/*object*/ $parent)
    {
        $this->initId();

        parent::__construct();

        $this->parent = $parent;

        $this->initForm();
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function checkInput() : bool
    {
        return parent::checkInput();
    }


    /**
     * @return bool
     *
     * @deprecated
     */
    public function storeForm() : bool
    {
        if (!$this->storeFormCheck()) {
            return false;
        }

        $this->storeFormItems($this->fields);

        return true;
    }


    /**
     * @param string      $key
     * @param string|null $default
     *
     * @return string
     *
     * @deprecated
     */
    public function txt(string $key,/*?*/ string $default = null) : string
    {
        if ($default !== null) {
            return self::plugin()->translate($key, static::LANG_MODULE, [], true, "", $default);
        } else {
            return self::plugin()->translate($key, static::LANG_MODULE);
        }
    }


    /**
     * @param string $key
     *
     * @return mixed
     *
     * @deprecated
     */
    protected abstract function getValue(string $key);


    /**
     * @deprecated
     */
    protected function initAction() : void
    {
        $this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));
    }


    /**
     * @deprecated
     */
    protected abstract function initCommands() : void ;


    /**
     * @deprecated
     */
    protected abstract function initFields() : void ;


    /**
     * @deprecated
     */
    protected abstract function initId() : void ;


    /**
     * @deprecated
     */
    protected abstract function initTitle() : void ;


    /**
     * @return bool
     *
     * @deprecated
     */
    protected final function storeFormCheck() : bool
    {
        $this->setValuesByPost();

        $this->check_input_called = false; // Fix 'Error: ilPropertyFormGUI->checkInput() called twice.'

        if (!$this->checkInput()) {
            return false;
        }

        return true;
    }


    /**
     * @param string $key
     * @param mixed  $value
     *
     * @deprecated
     */
    protected abstract function storeValue(string $key, $value) : void ;


    /**
     * @param array                               $fields
     * @param ilPropertyFormGUI|ilFormPropertyGUI $parent_item
     *
     * @throws PropertyFormGUIException $fields needs to be an array!
     * @throws PropertyFormGUIException Class $class not exists!
     * @throws PropertyFormGUIException $item must be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!
     * @throws PropertyFormGUIException $options needs to be an array!
     *
     * @deprecated
     */
    private final function getFields(array $fields, $parent_item) : void
    {
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

            if (!($item instanceof ilFormPropertyGUI || $item instanceof ilFormSectionHeaderGUI || $item instanceof ilRadioOption || $item instanceof TabsInputGUITab)) {
                throw new PropertyFormGUIException("\$item must be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!", PropertyFormGUIException::CODE_INVALID_FIELD);
            }

            $this->items_cache[$key] = $item;

            if ($item instanceof ilFormPropertyGUI) {
                if (!isset($field[self::PROPERTY_VALUE])) {
                    if (!($parent_item instanceof MultiLineNewInputGUI) && !($parent_item instanceof TabsInputGUI)
                        && !($parent_item instanceof TabsInputGUITab)
                    ) {
                        $value = $this->getValue($key);

                        Items::setValueToItem($item, $value);
                    }
                }
            }

            if (is_array($field[self::PROPERTY_SUBITEMS])) {
                $this->getFields($field[self::PROPERTY_SUBITEMS], $item);
            }

            if ($parent_item instanceof TabsInputGUI) {
                $parent_item->addTab($item);
            } else {
                if ($parent_item instanceof TabsInputGUITab || $parent_item instanceof MultiLineInputGUI || $parent_item instanceof MultiLineNewInputGUI) {
                    $parent_item->addInput($item);
                } else {
                    if ($parent_item instanceof ilRadioGroupInputGUI) {
                        $parent_item->addOption($item);
                    } else {
                        if ($parent_item instanceof ilPropertyFormGUI) {
                            $parent_item->addItem($item);
                        } else {
                            if ($item instanceof ilFormSectionHeaderGUI) {
                                // Fix 'Call to undefined method ilFormSectionHeaderGUI::setParent()'
                                Closure::bind(function (ilFormSectionHeaderGUI $item) : void {
                                    $this->sub_items[]
                                        = $item; // https://github.com/ILIAS-eLearning/ILIAS/blob/b8a2a3a203d8fb5bab988849ab43616be7379551/Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php#L45
                                }, $parent_item, ilSubEnabledFormPropertyGUI::class)($item);
                            } else {
                                $parent_item->addSubItem($item);
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * @deprecated
     */
    private final function initForm() : void
    {
        $this->initAction();

        $this->initCommands();

        $this->initTitle();

        $this->initItems();
    }


    /**
     * @deprecated
     */
    private final function initItems() : void
    {
        $this->initFields();

        $this->getFields($this->fields, $this);
    }


    /**
     * @param array $fields
     *
     * @deprecated
     */
    private final function storeFormItems(array $fields) : void
    {
        foreach ($fields as $key => $field) {
            if (isset($this->items_cache[$key])) {
                $item = $this->items_cache[$key];

                if ($item instanceof ilFormPropertyGUI) {
                    $value = Items::getValueFromItem($item);

                    $this->storeValue($key, $value);
                }

                if (is_array($field[self::PROPERTY_SUBITEMS])) {
                    if (!($item instanceof MultiLineInputGUI) && !($item instanceof MultiLineNewInputGUI) && !($item instanceof TabsInputGUI) && !($item instanceof TabsInputGUITab)) {
                        $this->storeFormItems($field[self::PROPERTY_SUBITEMS]);
                    }
                }
            }
        }
    }
}
