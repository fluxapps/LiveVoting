<?php

namespace srag\DIC\LiveVoting\DIC;

use ILIAS\DI\Container;
use srag\DIC\LiveVoting\Database\DatabaseDetector;
use srag\DIC\LiveVoting\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\LiveVoting\DIC
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
