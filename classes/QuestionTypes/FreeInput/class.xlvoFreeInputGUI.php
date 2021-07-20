<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use LiveVoting\Js\xlvoJs;
use LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputCategorizeGUI;
use LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputSubFormGUI;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\QuestionTypes\xlvoQuestionTypesGUI;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\UIComponent\GlyphGUI;
use srag\CustomInputGUIs\LiveVoting\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\LiveVoting\TextAreaInputGUI\TextAreaInputGUI;
use srag\CustomInputGUIs\LiveVoting\TextInputGUI\TextInputGUI;
use srag\CustomInputGUIs\LiveVoting\HiddenInputGUI\HiddenInputGUI;

/**
 * Class xlvoFreeInputGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoFreeInputGUI: xlvoVoter2GUI
 */
class xlvoFreeInputGUI extends xlvoQuestionTypesGUI
{

    const CMD_UNVOTE_ALL = 'unvoteAll';
    const CMD_SUBMIT = 'submit';
    const F_VOTE_MULTI_LINE_INPUT = 'vote_multi_line_input';
    const F_FREE_INPUT = 'free_input';
    const F_VOTE_ID = 'vote_id';

    const BUTTON_CATEGORIZE = 'btn_categorize';
    /**
     * @var ilTemplate
     */
    protected $tpl;


    /**
     * @param bool $current
     */
    public function initJS($current = false)
    {
        MultiLineNewInputGUI::init();
        xlvoJs::getInstance()->api($this)->name(xlvoQuestionTypes::FREE_INPUT)->category('QuestionTypes')->init();
    }


    /**
     *
     */
    protected function submit()
    {
        $input_gui = $this->getTextInputGUI("", self::F_FREE_INPUT);

        $this->manager->unvoteAll();
        if ($this->manager->getVoting()->isMultiFreeInput()) {
            $array = array();
            foreach (filter_input(INPUT_POST, self::F_VOTE_MULTI_LINE_INPUT, FILTER_DEFAULT, FILTER_FORCE_ARRAY) as $item) {
                $input = ilUtil::secureString($item[self::F_FREE_INPUT]);
                if (!empty($input) && strlen($input) <= $input_gui->getMaxLength()) {
                    $array[] = array(
                        "input"   => $input,
                        "vote_id" => $item[self::F_VOTE_ID],
                    );
                }
            }
            $this->manager->inputAll($array);
        } else {
            $input = ilUtil::secureString(filter_input(INPUT_POST, self::F_FREE_INPUT));
            if (!empty($input) && strlen($input) <= $input_gui->getMaxLength()) {
                $this->manager->inputOne(array(
                    "input"   => $input,
                    "vote_id" => filter_input(INPUT_POST, self::F_VOTE_ID),
                ));
            }
        }
    }


    /* *
     *
     * /
    protected function clear() {
        $this->manager->clear();
        $this->afterSubmit();
    }*/

    /**
     * @return string
     */
    public function getMobileHTML()
    {
        $this->tpl = self::plugin()->template('default/QuestionTypes/FreeInput/tpl.free_input.html');
        $this->render();

        return $this->tpl->get() . xlvoJs::getInstance()->name(xlvoQuestionTypes::FREE_INPUT)->category('QuestionTypes')->getRunCode();
    }


    /**
     * @param string $a_title
     * @param string $a_postvar
     *
     * @return ilTextInputGUI|ilTextAreaInputGUI
     */
    protected function getTextInputGUI($a_title = "", $a_postvar = "")
    {
        switch (intval($this->manager->getVoting()->getAnswerField())) {
            case xlvoFreeInputSubFormGUI::ANSWER_FIELD_MULTI_LINE:
                $input_gui = new TextAreaInputGUI($a_title, $a_postvar);
                $input_gui->setMaxlength(1000);
                break;

            case xlvoFreeInputSubFormGUI::ANSWER_FIELD_SINGLE_LINE:
            default:
                $input_gui = new TextInputGUI($a_title, $a_postvar);
                $input_gui->setMaxLength(200);
                break;
        }

        return $input_gui;
    }


    /**
     * @return ilPropertyFormGUI
     */
    protected function getForm()
    {
        if ($this->manager->getVoting()->isMultiFreeInput()) {
            return $this->getMultiForm();
        } else {
            return $this->getSingleForm();
        }
    }


    /**
     * @return ilPropertyFormGUI
     */
    protected function getSingleForm()
    {
        $form = new ilPropertyFormGUI();
        $form->setFormAction(self::dic()->ctrl()->getFormAction($this));
        $form->setId('xlvo_free_input');

        $votes = array_values($this->manager->getVotesOfUser(true));
        $vote = array_shift($votes);

        $an = $this->getTextInputGUI($this->txt('input'), self::F_FREE_INPUT);
        $hi2 = new HiddenInputGUI(self::F_VOTE_ID);

        if ($vote instanceof xlvoVote) {
            if ($vote->isActive()) {
                $an->setValue($vote->getFreeInput());
            }
            $hi2->setValue($vote->getId());
            //$form->addCommandButton(self::CMD_CLEAR, $this->txt(self::CMD_CLEAR));
        }

        $form->addItem($an);
        $form->addItem($hi2);
        $form->addCommandButton(self::CMD_SUBMIT, $this->txt('send'));

        return $form;
    }


    /**
     * @return ilPropertyFormGUI
     */
    protected function getMultiForm()
    {
        $form = new ilPropertyFormGUI();
        $form->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $xlvoVotes = $this->manager->getVotesOfUser();
        if (count($xlvoVotes) > 0) {
            $te = new ilNonEditableValueGUI();
            $te->setValue($this->txt('your_input'));
            $form->addItem($te);
            //$form->addCommandButton(self::CMD_CLEAR, $this->txt('delete_all'));
        }

        $mli = new MultiLineNewInputGUI($this->txt('answers'), self::F_VOTE_MULTI_LINE_INPUT);
        $te = $this->getTextInputGUI($this->txt('text'), self::F_FREE_INPUT);

        $hi2 = new HiddenInputGUI(self::F_VOTE_ID);
        $mli->addInput($te);
        $mli->addInput($hi2);

        $form->addItem($mli);
        $array = array();
        foreach ($xlvoVotes as $xlvoVote) {
            $array[] = array(
                self::F_FREE_INPUT => $xlvoVote->getFreeInput(),
                self::F_VOTE_ID    => $xlvoVote->getId(),
            );
        }

        $form->setValuesByArray(array(self::F_VOTE_MULTI_LINE_INPUT => $array));
        $form->addCommandButton(self::CMD_SUBMIT, $this->txt('send'));

        return $form;
    }


    /**
     * @return ilButtonBase[]
     * @throws \srag\DIC\LiveVoting\Exception\DICException
     */
    public function getButtonInstances()
    {
        if (!$this->manager->getPlayer()->isShowResults()) {
            return array();
        }

        $b = ilLinkButton::getInstance();
        $b->setId(self::BUTTON_CATEGORIZE);
        $b->setUrl('#');

        if ($this->getButtonsStates()[self::BUTTON_CATEGORIZE] == 'true') {
            $b->setCaption(GlyphGUI::get('folder-close') . '&nbsp' . self::plugin()->translate('categorize_done', 'btn'), false);
        } else {
            $b->setCaption(GlyphGUI::get('folder-open') . '&nbsp' . self::plugin()->translate('categorize', 'btn'), false);
        }

        return array($b);
    }


    /**
     * @param $button_id
     * @param $data
     */
    public function handleButtonCall($button_id, $data)
    {
        $data = $this->getButtonsStates()[self::BUTTON_CATEGORIZE] == 'true' ? 'false' : 'true';
        $this->saveButtonState($button_id, $data);
    }


    /**
     *
     */
    protected function render()
    {
        $form = $this->getForm();

        $this->tpl->setVariable('FREE_INPUT_FORM', $form->getHTML());
    }
}
