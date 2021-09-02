<?php

namespace srag\DIC\LiveVoting\Version;

/**
 * Class Version
 *
 * @package srag\DIC\LiveVoting\Version
 */
final class Version implements VersionInterface
{

    /**
     * Version constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getILIASVersion() : string
    {
        return ILIAS_VERSION_NUMERIC;
    }


    /**
     * @inheritDoc
     */
    public function is6() : bool
    {
        return $this->isMinVersion(self::ILIAS_VERSION_6);
    }


    /**
     * @inheritDoc
     */
    public function is7() : bool
    {
        return $this->isMinVersion(self::ILIAS_VERSION_7);
    }


    /**
     * @inheritDoc
     */
    public function is8() : bool
    {
        return $this->isMinVersion(self::ILIAS_VERSION_8);
    }


    /**
     * @inheritDoc
     */
    public function isEqual(string $version) : bool
    {
        return (version_compare($this->getILIASVersion(), $version) === 0);
    }


    /**
     * @inheritDoc
     */
    public function isGreater(string $version) : bool
    {
        return (version_compare($this->getILIASVersion(), $version) > 0);
    }


    /**
     * @inheritDoc
     */
    public function isLower(string $version) : bool
    {
        return (version_compare($this->getILIASVersion(), $version) < 0);
    }


    /**
     * @inheritDoc
     */
    public function isMaxVersion(string $version) : bool
    {
        return (version_compare($this->getILIASVersion(), $version) <= 0);
    }


    /**
     * @inheritDoc
     */
    public function isMinVersion(string $version) : bool
    {
        return (version_compare($this->getILIASVersion(), $version) >= 0);
    }
}
