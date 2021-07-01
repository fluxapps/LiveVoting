<?php

namespace LiveVoting\QuestionTypes\CorrectOrder;

use ilCheckboxInputGUI;
use ilException;
use ilFormPropertyGUI;
use ilNumberInputGUI;
use InvalidArgumentException;
use LiveVoting\Exceptions\xlvoSubFormGUIHandleFieldException;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoSubFormGUI;
use srag\CustomInputGUIs\LiveVoting\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\LiveVoting\TextInputGUI\TextInputGUI;
use srag\CustomInputGUIs\LiveVoting\HiddenInputGUI\HiddenInputGUI;
use ilTemplate;
use ilGlobalPageTemplate;

/**
 * Class xlvoCorrectOrderSubFormGUI
 *
 * @package LiveVoting\QuestionTypes\CorrectOrder
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoCorrectOrderSubFormGUI extends xlvoSubFormGUI
{

    const F_OPTIONS = 'options';
    const F_TEXT = 'text';
    const F_ID = 'id';
    const F_POSITION = 'position';
    const F_CORRECT_POSITION = 'correct_position';
    const OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE = 'option_randomise_option_after_save';
    const OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE_INFO = 'option_randomise_option_after_save_info';
    const CSS_FILE_PATH = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/QuestionTypes/CorrectOrder/correct_order_form.css';

    /**
     * @var xlvoOption[]
     */
    protected $options = array();

    public function addJsAndCss(ilGlobalPageTemplate $ilTemplate)
    {
        $ilTemplate->addCSS(self::CSS_FILE_PATH);
    }

    /**
     *
     */
    protected function initFormElements()
    {

        $xlvoMultiLineInputGUI = new MultiLineNewInputGUI($this->txt(self::F_OPTIONS), self::F_OPTIONS);
        $xlvoMultiLineInputGUI->setShowInputLabel(false);
        $xlvoMultiLineInputGUI->setShowSort(false);

        $randomiseOptionSequenceAfterSave = new ilCheckboxInputGUI($this->txt(self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE), self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE);
        $randomiseOptionSequenceAfterSave->setOptionTitle($this->txt(self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE_INFO));
        //$xlvoMultiLineInputGUI->setPositionMovable(true); // Allow move position
        $randomiseOptionSequenceAfterSave->setChecked($this->getXlvoVoting()->getRandomiseOptionSequence()); // Should shuffled?

        $h = new HiddenInputGUI(self::F_ID);
        $xlvoMultiLineInputGUI->addInput($h);

        /*if (!$this->getXlvoVoting()->getRandomiseOptionSequence()) {
            // Allow input correct position if not shuffled*/
        $position = new ilNumberInputGUI($this->txt('option_correct_position'), self::F_CORRECT_POSITION);
        $position->setRequired(true);
        $position->setMinValue(1);
        $position->setSize(2);
        $position->setMaxLength(2);
        /*} else {
            // Only display correct order as text if shuffled
            $position = new ilNonEditableValueGUI("", self::F_CORRECT_POSITION, true);
        }*/
        $xlvoMultiLineInputGUI->addInput($position);

        $te = new TextInputGUI($this->txt('option_text'), self::F_TEXT);
        $xlvoMultiLineInputGUI->addInput($te);

        $this->addFormElement($randomiseOptionSequenceAfterSave);
        $this->addFormElement($xlvoMultiLineInputGUI);
    }


    /**
     * @param ilFormPropertyGUI $element
     * @param string|array      $value
     *
     * @throws xlvoSubFormGUIHandleFieldException|ilException
     */
    protected function handleField(ilFormPropertyGUI $element, $value)
    {
        switch ($element->getPostVar()) {
            case self::F_OPTIONS:
                $pos = 1;
                foreach ($value as $item) {
                    /**
                     * @var xlvoOption $xlvoOption
                     */
                    $xlvoOption = xlvoOption::findOrGetInstance($item[self::F_ID]);
                    $xlvoOption->setText($item[self::F_TEXT]);
                    $xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
                    $xlvoOption->setVotingId($this->getXlvoVoting()->getId());
                    $xlvoOption->setPosition($pos);
                    /*if (!$this->getXlvoVoting()->getRandomiseOptionSequence()) {
                        // Correct position can only be inputed if not shuffle*/
                    $xlvoOption->setCorrectPosition($item[self::F_CORRECT_POSITION]);
                    /*}*/
                    $xlvoOption->setType($this->getXlvoVoting()->getVotingType());
                    $this->options[] = $xlvoOption;
                    $pos++;
                }
                break;
            case self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE:
                $value = boolval($value);
                $this->getXlvoVoting()->setRandomiseOptionSequence($value);
                break;
            default:
                throw new ilException('Unknown element can not set the value.');
        }
    }


    /**
     * @param ilFormPropertyGUI $element
     *
     * @return string|int|float|bool|array
     * @throws ilException
     */
    protected function getFieldValue(ilFormPropertyGUI $element)
    {
        if ($this->getXlvoVoting()->getRandomiseOptionSequence()) {
            // Sort options by correct position if shuffled
            $this->options = xlvoOption::where(array("voting_id" => $this->getXlvoVoting()->getId()))->orderBy("correct_position")->get();
        } else {
            // Sort options by position if not shuffled
            $this->options = $this->getXlvoVoting()->getVotingOptions();
        }
        switch ($element->getPostVar()) {
            case self::F_OPTIONS:
                $array = [];
                foreach ($this->options as $option) {
                    $array[] = [
                        self::F_ID               => $option->getId(),
                        self::F_TEXT             => $option->getTextForEditor(),
                        self::F_POSITION         => $option->getPosition(),
                        self::F_CORRECT_POSITION => /*($this->getXlvoVoting()->getRandomiseOptionSequence() ? "<br>" : "")
							. */
                            $option->getCorrectPosition()/* . ($this->getXlvoVoting()->getRandomiseOptionSequence() ? "." : "")
						// Display as text whit dot and break if shuffled otherwise only position for input*/
                    ];
                }

                return $array;
            case self::OPTION_RANDOMIZE_OPTIONS_AFTER_SAVE:
                return $this->getXlvoVoting()->getRandomiseOptionSequence();
            default:
                throw new ilException('Unknown element can not get the value.');
                break;
        }
    }


    /**
     *
     */
    protected function handleOptions()
    {
        $ids = array();
        foreach ($this->options as $xlvoOption) {
            $xlvoOption->setVotingId($this->getXlvoVoting()->getId());
            $xlvoOption->store();
            $ids[] = $xlvoOption->getId();
        }
        $options = $this->getXlvoVoting()->getVotingOptions();

        foreach ($options as $xlvoOption) {
            if (!in_array($xlvoOption->getId(), $ids)) {
                $xlvoOption->delete();
            }
        }

        //randomize the order on save
        if ($this->getXlvoVoting()->getRandomiseOptionSequence()) {
            /*// First set correct position in the sequence of user has ordered
            foreach ($this->options as $i => $option) {
                $option->setCorrectPosition($option->getPosition());
            }*/
            // Then shuffle positions
            $this->randomiseOptionPosition($this->options);
        }

        foreach ($this->options as $option) {
            $option->store();
        }

        $this->getXlvoVoting()->setMultiFreeInput(true);
        $this->getXlvoVoting()->store();
    }


    /**
     * Randomises the position of the given options the position in the array is not modified at all.
     *
     * @param xlvoOption[] $options The options which should be randomised.
     *
     * @return void
     */
    private function randomiseOptionPosition(array &$options)
    {

        //reorder only if there is something to reorder
        if (count($options) < 2) {
            return;
        }

        $optionsLength = count($options);
        foreach ($options as $option) {
            $newPosition = rand(1, $optionsLength);
            $previousOption = $this->findOptionByPosition($options, $newPosition);
            $previousOption->setPosition($option->getPosition());
            $option->setPosition($newPosition);
        }

        //check if we got the correct result
        if ($this->isNotCorrectlyOrdered($options)) {
            return;
        }

        //we got the right result reshuffle
        $this->randomiseOptionPosition($options);
    }


    /**
     * Searches an option within the given option array by position.
     *
     * @param xlvoOption[] $options  The options which should be used to search the position.
     * @param int          $position The position which should be searched for.
     *
     * @return xlvoOption
     * @throws InvalidArgumentException Thrown if the position is not found within the given options.
     */
    private function findOptionByPosition(array &$options, $position)
    {
        foreach ($options as $option) {
            if ($option->getPosition() === $position) {
                return $option;
            }
        }

        throw new InvalidArgumentException("Supplied position \"$position\" can't be found within the given options.");
    }


    /**
     * Checks if at least one element is not correctly ordered.
     *
     * @param xlvoOption[] $options The options which should be checked.
     *
     * @return bool                     True if at least one element is not correctly ordered otherwise false.
     */
    private function isNotCorrectlyOrdered(array &$options)
    {
        $incorrectOrder = 0;
        foreach ($options as $option) {
            if (strcmp($option->getCorrectPosition(), strval($option->getPosition())) !== 0) {
                $incorrectOrder++;
            }
        }

        return $incorrectOrder > 0;
    }
}
