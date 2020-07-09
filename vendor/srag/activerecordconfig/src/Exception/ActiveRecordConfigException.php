<?php

namespace srag\ActiveRecordConfig\LiveVoting\Exception;

use ilException;

/**
 * Class ActiveRecordConfigException
 *
 * @package srag\ActiveRecordConfig\LiveVoting\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @deprecated
 */
final class ActiveRecordConfigException extends ilException
{

    /**
     * @var int
     *
     * @deprecated
     */
    const CODE_INVALID_FIELD = 1;
    /**
     * @var int
     *
     * @deprecated
     */
    const CODE_UNKOWN_COMMAND = 2;
    /**
     * @var int
     *
     * @deprecated
     */
    const CODE_INVALID_CONFIG_GUI_CLASS = 3;


    /**
     * ActiveRecordConfigException constructor
     *
     * @param string $message
     * @param int    $code
     *
     * @internal
     *
     * @deprecated
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
