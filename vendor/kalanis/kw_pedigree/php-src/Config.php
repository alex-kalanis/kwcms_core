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
 * This is mainly example of configuration, but you can use it
 */
class Config
{
    public static function init(string $sourceName = 'pedigree'): void
    {
        try { // try if this config exists
            Database\ConfigStorage::getInstance()->getConfig($sourceName);
        } catch (MapperException $ex) { // if not use our own - with possibility to set it from environment variables
            $type = getenv('KW_PEDIGREE_DB_TYPE');
            $host = getenv('KW_PEDIGREE_DB_HOST');
            $port = getenv('KW_PEDIGREE_DB_PORT');
            $user = getenv('KW_PEDIGREE_DB_USER');
            $pass = getenv('KW_PEDIGREE_DB_PASS');
            $db = getenv('KW_PEDIGREE_DB_NAME');
            Database\ConfigStorage::getInstance()->addConfig(
                Database\Config::init()->setTarget(
                    ((false !== $type) && !is_array($type)) ? strval($type) : IDriverSources::TYPE_PDO_MYSQL,
                    $sourceName,
                    ((false !== $host) && !is_array($host)) ? strval($host) : 'kwcms-mariadb',
                    ((false !== $port) && !is_array($port)) ? intval($port) : 3306,
                    ((false !== $user) && !is_array($user)) ? strval($user) : 'root',
                    ((false !== $pass) && !is_array($pass)) ? strval($pass) : '951357456852',
                    ((false !== $db) && !is_array($db)) ? strval($db) : 'kwcms'
                )
            );
        }
    }
}
