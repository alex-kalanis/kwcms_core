<?php

namespace kalanis\kw_confs\Loaders;


use kalanis\kw_confs\Interfaces\IConf;
use kalanis\kw_confs\Interfaces\ILoader;


/**
 * Class ClassLoader
 * @package kalanis\kw_confs\Loaders
 * Load lang data from class
 */
class ClassLoader implements ILoader
{
    /** @var IConf[] */
    protected $confs = [];

    public function addClass(IConf $conf): self
    {
        $this->confs[get_class($conf)] = $conf;
        return $this;
    }

    public function load(string $module, string $conf = ''): ?array
    {
        foreach ($this->confs as $confClassName => $confClass) {
            if ($confClass->getConfName() == $module) {
                $confClass->setPart($conf);
                return $confClass->getSettings();
            }
        }
        return null;
    }
}
