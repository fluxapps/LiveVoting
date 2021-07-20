<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use LiveVoting\Js\xlvoJs;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\QuestionTypes\xlvoQuestionTypesGUI;
use LiveVoting\UIComponent\GlyphGUI;

/**
 * Class xlvoSingleVoteGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoSingleVoteGUI: xlvoVoter2GUI
 */
class xlvoSingleVoteGUI extends xlvoQuestionTypesGUI
{

    const BUTTON_TOGGLE_PERCENTAGE = 'toggle_percentage';


    /**
     * @param bool $current
     */
    public function initJS($current = false)
    {
        xlvoJs::getInstance()->api($this)->name(xlvoQuestionTypes::SINGLE_VOTE)->category('QuestionTypes')
            ->addLibToHeader('jquery.ui.touch-punch.min.js')->init();
    }


    /**
     *
     */
    protected function submit()
    {
        $this->manager->vote($_GET['option_id']);
    }


    /**
     * @return array
     */
    public function getButtonInstances()
    {
        if (!$this->manager->getPlayer()->isShowResults()) {
            return array();
        }
        $states = $this->getButtonsStates();
        $t = ilLinkButton::getInstance();
        $t->setId(self::BUTTON_TOGGLE_PERCENTAGE);
        if ($states[self::BUTTON_TOGGLE_PERCENTAGE]) {
            $t->setCaption(' %', false);
        } else {
            $t->setCaption(GlyphGUI::get('user'), false);
        }

        return array($t);
    }


    /**
     * @param $button_id
     * @param $data
     */
    public function handleButtonCall($button_id, $data)
    {
        $states = $this->getButtonsStates();
        $this->saveButtonState($button_id, !$states[$button_id]);
    }


    /**
     * @return string
     */
    public function getMobileHTML()
    {
        $tpl = self::plugin()->template('default/QuestionTypes/SingleVote/tpl.single_vote.html', false);
        $answer_count = 64;
        foreach ($this->manager->getVoting()->getVotingOptions() as $xlvoOption) {
            $answer_count++;
            self::dic()->ctrl()->setParameter($this, 'option_id', $xlvoOption->getId());
            $tpl->setCurrentBlock('option');
            $tpl->setVariable('TITLE', $xlvoOption->getTextForPresentation());
            $tpl->setVariable('LINK', self::dic()->ctrl()->getLinkTarget($this, self::CMD_SUBMIT));
            $tpl->setVariable('OPTION_LETTER', chr($answer_count));
            if ($this->manager->hasUserVotedForOption($xlvoOption)) {
                $tpl->setVariable('BUTTON_STATE', 'btn-primary');
                $tpl->setVariable('ACTION', $this->txt('unvote'));
            } else {
                $tpl->setVariable('BUTTON_STATE', 'btn-default');
                $tpl->setVariable('ACTION', $this->txt('vote'));
            }
            $tpl->parseCurrentBlock();
        }

        return $tpl->get() . xlvoJs::getInstance()->name(xlvoQuestionTypes::SINGLE_VOTE)->category('QuestionTypes')->getRunCode();
    }
}
