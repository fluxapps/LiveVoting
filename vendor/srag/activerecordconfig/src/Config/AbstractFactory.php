<?php

namespace srag\ActiveRecordConfig\LiveVoting\Config;

use srag\DIC\LiveVoting\DICTrait;

/**
 * Class AbstractFactory
 *
 * @package srag\ActiveRecordConfig\LiveVoting\Config
 */
abstract class AbstractFactory
{

    use DICTrait;

    /**
     * AbstractFactory constructor
     */
    protected function __construct()
    {

    }


    /**
     * @return Config
     */
    public function newInstance() : Config
    {
        $config = new Config();

        return $config;
    }
}
