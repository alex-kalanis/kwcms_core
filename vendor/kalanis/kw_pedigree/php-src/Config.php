<?php

namespace kalanis\kw_pedigree;


use kalanis\kw_mapper\Interfaces\IDriverSources;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Storage\Database;


/**
 * Class Config
 * @package kalanis\kw_pedigree
 * Default configuration for testing DB in kw_pedigree
 * You can call your own implementation and settings in bootstrap
 */
class Config
{
    public static function init(): void
    {
        try { // try if this config exists
            Database\ConfigStorage::getInstance()->getConfig('pedigree');
        } catch (MapperException $ex) { // if not use our own
            Database\ConfigStorage::getInstance()->addConfig(
                Database\Config::init()->setTarget(
                    IDriverSources::TYPE_PDO_MYSQL, 'pedigree', 'localhost', 3306, 'kwdeploy', 'testingpass', 'kw_deploy'
                )
            );
        }
    }
}
