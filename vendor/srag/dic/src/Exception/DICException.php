<?php

namespace srag\DIC\LiveVoting\Exception;

use ilException;

/**
 * Class DICException
 *
 * @package srag\DIC\LiveVoting\Exception
 */
final class DICException extends ilException
{

    /**
     * @var int
     */
    const CODE_INVALID_PLUGIN_CLASS = 3;
    /**
     * @var int
     */
    const CODE_MISSING_CONST_PLUGIN_CLASS_NAME = 2;
    /**
     * @var int
     */
    const CODE_MISUSE_TRANSLATE_WITH_SPRINTF = 4;
    /**
     * @var int
     */
    const CODE_NOT_SUPPORTED_ON_CURRENT_ILIAS_VERSION = 1;
    /**
     * @var int
     */
    const CODE_OUTPUT_INVALID_VALUE = 5;


    /**
     * DICException constructor
     *
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message, int $code = self::CODE_NOT_SUPPORTED_ON_CURRENT_ILIAS_VERSION)
    {
        parent::__construct($message, $code);
    }
}
