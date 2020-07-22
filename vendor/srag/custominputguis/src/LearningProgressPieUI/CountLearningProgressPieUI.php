<?php

namespace srag\CustomInputGUIs\LiveVoting\LearningProgressPieUI;

/**
 * Class CountLearningProgressPieUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\LearningProgressPieUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CountLearningProgressPieUI extends AbstractLearningProgressPieUI
{

    /**
     * @var int[]
     */
    protected $count = [];


    /**
     * @param int[] $count
     *
     * @return self
     */
    public function withCount(array $count) : self
    {
        $this->count = $count;

        return $this;
    }


    /**
     * @inheritDoc
     */
    protected function parseData() : array
    {
        if (count($this->count) > 0) {
            return $this->count;
        } else {
            return [];
        }
    }


    /**
     * @inheritDoc
     */
    protected function getCount() : int
    {
        return array_reduce($this->count, function (int $sum, int $count) : int {
            return ($sum + $count);
        }, 0);
    }
}
