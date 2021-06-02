<?php

namespace srag\DIC\LiveVoting\Output;

use ilTemplateException;
use JsonSerializable;
use srag\DIC\LiveVoting\Exception\DICException;
use stdClass;

/**
 * Interface OutputInterface
 *
 * @package srag\DIC\LiveVoting\Output
 */
interface OutputInterface
{

    /**
     * Get HTML of GUI
     *
     * @param string|object|array $value html or GUI instance
     *
     * @return string HTML
     *
     * @throws DICException Class {get_class($value)} is not supported for output!
     * @throws ilTemplateException
     */
    public function getHTML($value) : string;


    /**
     * Output HTML or GUI
     *
     * @param string|object|array $value         html or GUI instance
     * @param bool                $main_template Display main skin?
     * @param bool                $show          Show main template?
     *
     * @throws DICException Class {get_class($value)} is not supported for output!
     * @throws ilTemplateException
     */
    public function output($value, bool $show = false, bool $main_template = true)/*: void*/ ;


    /**
     * Output JSON
     *
     * @param string|int|double|bool|array|stdClass|null|JsonSerializable $value JSON value
     *
     * @throws DICException {get_class($value)} is not a valid JSON value!
     */
    public function outputJSON($value)/*: void*/ ;
}
