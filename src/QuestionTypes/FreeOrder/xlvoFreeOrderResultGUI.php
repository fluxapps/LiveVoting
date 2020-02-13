<?php

namespace LiveVoting\QuestionTypes\FreeOrder;

use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoResultGUI;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoFreeOrderResultGUI
 *
 * @package LiveVoting\QuestionTypes\FreeOrder
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoFreeOrderResultGUI extends xlvoResultGUI
{

    /**
     * @param xlvoVote[] $votes
     *
     * @return string
     */
    public function getTextRepresentation(array $votes)
    {
        $strings = array();
        if (!count($votes)) {
            return "";
        } else {
            $vote = array_shift($votes);
        }
        $json_decode = json_decode($vote->getFreeInput());
        if (!is_array($json_decode)) {
            return "";
        }
        foreach ($json_decode as $option_id) {
            $xlvoOption = $this->options[$option_id];
            if ($xlvoOption instanceof xlvoOption) {
                $strings[] = $xlvoOption->getTextForPresentation();
            } else {
                $strings[] = self::plugin()->translate("common_option_no_longer_available");
            }
        }

        return implode(", ", $strings);
    }


    /**
     * @param xlvoVote[] $votes
     *
     * @return string
     */
    public function getAPIRepresentation(array $votes)
    {
        $strings = array();
        if (!count($votes)) {
            return "";
        } else {
            $vote = array_shift($votes);
        }
        $json_decode = json_decode($vote->getFreeInput());
        if (!is_array($json_decode)) {
            return "";
        }
        foreach ($json_decode as $option_id) {
            $strings[] = $this->options[$option_id]->getText();
        }

        return implode(", ", $strings);
    }
}
