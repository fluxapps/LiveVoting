<?php

namespace srag\CustomInputGUIs\LiveVoting\WeekdayInputGUI;

use ilCalendarUtil;
use ilFormPropertyGUI;
use ilTableFilterItem;
use ilTemplate;
use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class WeekdayInputGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\WeekdayInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class WeekdayInputGUI extends ilFormPropertyGUI implements ilTableFilterItem
{

    use DICTrait;

    const TYPE = 'weekday';
    /**
     * @var array
     */
    protected $value = [];


    /**
     * WeekdayInputGUI constructor
     *
     * @param string $a_title
     * @param string $a_postvar
     */
    public function __construct(/*string*/ $a_title, /*string*/ $a_postvar)
    {
        parent::__construct($a_title, $a_postvar);

        $this->setType(self::TYPE);
    }


    /**
     * @return bool
     */
    public function checkInput()/*: bool*/
    {
        return ($_POST[$this->getPostVar()] == null) || (count($_POST[$this->getPostVar()]) <= 7);
    }


    /**
     * @inheritDoc
     */
    public function getTableFilterHTML()/*: string*/
    {
        $html = $this->render();

        return $html;
    }


    /**
     * Get Value.
     *
     * @return array Value
     */
    public function getValue()/*: array*/
    {
        return $this->value;
    }


    /**
     * @param ilTemplate $tpl
     */
    public function insert(ilTemplate $tpl)/*: void*/
    {
        $html = $this->render();

        $tpl->setCurrentBlock("prop_generic");
        $tpl->setVariable("PROP_GENERIC", $html);
        $tpl->parseCurrentBlock();
    }


    /**
     * @return string
     */
    public function render()/*: string*/
    {
        $tpl = new Template(__DIR__ . "/templates/tpl.weekday_input.html", true, true);

        $days = [1 => 'MO', 2 => 'TU', 3 => 'WE', 4 => 'TH', 5 => 'FR', 6 => 'SA', 7 => 'SU'];

        for ($i = 1; $i < 8; $i++) {
            $tpl->setCurrentBlock('byday_simple');

            if (in_array($days[$i], $this->getValue())) {
                $tpl->setVariable('BYDAY_WEEKLY_CHECKED', 'checked="checked"');
            }
            $tpl->setVariable('TXT_ON', self::dic()->language()->txt('cal_on'));
            $tpl->setVariable('BYDAY_WEEKLY_VAL', $days[$i]);
            $tpl->setVariable('TXT_DAY_SHORT', ilCalendarUtil::_numericDayToString($i, false));
            $tpl->setVariable('POSTVAR', $this->getPostVar());
            $tpl->parseCurrentBlock();
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * Set Value.
     *
     * @param array $a_value Value
     */
    public function setValue(/*array*/ $a_value)/*: void*/
    {
        $this->value = $a_value;
    }


    /**
     * Set value by array
     *
     * @param array $a_values
     *
     * @internal param object $a_item Item
     */
    public function setValueByArray(/*array*/ $a_values)/*: void*/
    {
        $this->setValue($a_values[$this->getPostVar()] ? $a_values[$this->getPostVar()] : []);
    }
}
