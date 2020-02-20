<?php

namespace LiveVoting\QuestionTypes\CorrectOrder;

use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoResultGUI;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoCorrectOrderResultGUI
 *
 * @package LiveVoting\QuestionTypes\CorrectOrder
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoCorrectOrderResultGUI extends xlvoResultGUI
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

        $correct_order_json = $this->getCorrectOrderJSON();
        $return = ($correct_order_json == $vote->getFreeInput())
            ? self::plugin()->translate("common_correct_order")
            : self::plugin()
                ->translate("common_incorrect_order");
        $return .= ": ";
        foreach (json_decode($vote->getFreeInput()) as $option_id) {
            $xlvoOption = $this->options[$option_id];
            if ($xlvoOption instanceof xlvoOption) {
                $strings[] = $xlvoOption->getTextForPresentation();
            } else {
                $strings[] = self::plugin()->translate("common_option_no_longer_available");
            }
        }

        return $return . implode(", ", $strings);
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
        $correct_order_json = $this->getCorrectOrderJSON();
        $return = ($correct_order_json == $vote->getFreeInput())
            ? self::plugin()->translate("common_correct_order")
            : self::plugin()
                ->translate("common_incorrect_order");
        $return .= ": ";
        foreach (json_decode($vote->getFreeInput()) as $option_id) {
            $strings[] = $this->options[$option_id]->getText();
        }

        return $return . implode(", ", $strings);
    }


    /**
     * @return string
     */
    protected function getCorrectOrderJSON()
    {
        $correct_order_ids = array();
        foreach ($this->options as $option) {
            $correct_order_ids[(int) $option->getCorrectPosition()] = $option->getId();
        };
        ksort($correct_order_ids);
        $correct_order_json = json_encode(array_values($correct_order_ids));

        return $correct_order_json;
    }
}
