<?php

namespace LiveVoting\QuestionTypes\FreeInput;

use LiveVoting\Display\Bar\xlvoBarFreeInputsGUI;
use LiveVoting\Display\Bar\xlvoBarGroupingCollectionGUI;
use LiveVoting\Exceptions\xlvoPlayerException;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoInputResultsGUI;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Voting\xlvoVotingManager2;
use srag\CustomInputGUIs\LiveVoting\Waiter\Waiter;
use xlvoFreeInputGUI;
use xlvoPlayerGUI;

/**
 * Class xlvoFreeInputResultsGUI
 *
 * @package LiveVoting\QuestionTypes\FreeInput
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputResultsGUI extends xlvoInputResultsGUI
{

    /**
     * @var bool
     */
    protected $edit_mode = false;


    public function __construct(xlvoVotingManager2 $manager, xlvoVoting $voting)
    {
        parent::__construct($manager, $voting);
    }


    /**
     * @return string
     * @throws \ilException
     */
    public function getHTML()
    {
        $button_states = $this->manager->getPlayer()->getButtonStates();
        $this->edit_mode = ($button_states[xlvoFreeInputGUI::BUTTON_CATEGORIZE] == 'true');

        $tpl = self::plugin()->template('default/QuestionTypes/FreeInput/tpl.free_input_results.html');

        $categories = new xlvoFreeInputCategoriesGUI($this->manager, $this->edit_mode);

        $bars = new xlvoBarGroupingCollectionGUI();
        $bars->setRemovable($this->edit_mode);
        $bars->setShowTotalVotes(true);

        /**
         * @var xlvoOption $option
         */
        $option = $this->manager->getVoting()->getFirstVotingOption();

        /**
         * @var xlvoVote[] $votes
         */
        $votes = $this->manager->getVotesOfOption($option->getId());
        foreach ($votes as $vote) {
            if ($cat_id = $vote->getFreeInputCategory()) {
                try {
                    $categories->addBar(new xlvoBarFreeInputsGUI($this->manager->getVoting(), $vote), $cat_id);
                } catch (xlvoPlayerException $e) {
                    if ($e->getCode() == xlvoPlayerException::CATEGORY_NOT_FOUND) {
                        $bars->addBar(new xlvoBarFreeInputsGUI($this->manager->getVoting(), $vote));
                    }
                }
            } else {
                $bars->addBar(new xlvoBarFreeInputsGUI($this->manager->getVoting(), $vote));
            }
        }

        $bars->setTotalVotes(count($votes));

        $tpl->setVariable('ANSWERS', $bars->getHTML());
        $tpl->setVariable('CATEGORIES', $categories->getHTML());
        if ($this->edit_mode) {
            $tpl->setVariable('LABEL_ADD_CATEGORY', self::plugin()->translate('btn_add_category'));
            $tpl->setVariable('PLACEHOLDER_ADD_CATEGORY', self::plugin()->translate('category_title'));
            $tpl->setVariable('LABEL_ADD_ANSWER', self::plugin()->translate('btn_add_answer'));
            $tpl->setVariable('PLACEHOLDER_ADD_ANSWER', self::plugin()->translate('voter_answer'));
            $tpl->setVariable('BASE_URL', self::dic()->ctrl()->getLinkTargetByClass(xlvoPlayerGUI::class, xlvoPlayerGUI::CMD_API_CALL, "", true));
        }

        return $tpl->get();
    }


    public function reset()
    {
        parent::reset();
        /** @var xlvoFreeInputCategory $category */
        foreach (
            xlvoFreeInputCategory::where(['voting_id' => $this->manager->getVoting()->getId()])
                ->get() as $category
        ) {
            $category->delete();
        }
    }


    /**
     * @throws \srag\DIC\LiveVoting\Exception\DICException
     */
    public static function addJsAndCss()
    {
        Waiter::init(Waiter::TYPE_WAITER);
        self::dic()->ui()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/node_modules/dragula/dist/dragula.js');
        self::dic()->ui()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/js/QuestionTypes/FreeInput/xlvoFreeInputCategorize.js');
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/node_modules/dragula/dist/dragula.min.css');
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/QuestionTypes/FreeInput/free_input.css');
    }


    /**
     * @param xlvoVote[] $votes
     *
     * @return string
     */
    public function getTextRepresentationForVotes(array $votes)
    {
        $string_votes = array();
        foreach ($votes as $vote) {
            $string_votes[] = str_replace(["\r\n", "\r", "\n"], " ", $vote->getFreeInput());
        }

        return implode(", ", $string_votes);
    }
}
