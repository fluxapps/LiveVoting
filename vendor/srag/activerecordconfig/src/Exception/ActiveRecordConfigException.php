<?php

namespace srag\ActiveRecordConfig\LiveVoting\Exception;

use ilException;

/**
 * Class ActiveRecordConfigException
 *
 * @package srag\ActiveRecordConfig\LiveVoting\Exception
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
    const CODE_INVALID_CONFIG_GUI_CLASS = 3;
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
