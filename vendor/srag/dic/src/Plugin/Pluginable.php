<?php

namespace srag\DIC\LiveVoting\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\LiveVoting\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
