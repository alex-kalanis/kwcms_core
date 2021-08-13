<?php

namespace kalanis\kw_short;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_paths\Path;
use kalanis\kw_tree\TWhereDir;


/**
 * Class ShortMessageAdapter
 * @package kalanis\kw_short
 * Connect short messages into system
 */
class ShortMessageAdapter
{
    use TWhereDir;

    protected $record = null;

    public function __construct(IVariables $inputs, Path $path)
    {
        Config::load('Short');
        $this->initWhereDir(new SessionAdapter(), $inputs);
        $this->record = new ShortMessage();
        $this->setDir($path->getDocumentRoot() . $path->getPathToSystemRoot() . $this->getWhereDir());
    }

    protected function setDir(string $dirPath): void
    {
        // need to pass currently selected directory
        $path = realpath($dirPath . DIRECTORY_SEPARATOR
            . Config::get('Short', 'name', 'index')
            . Config::get('Short', 'suff', '.short')
        );
        if (false === $path || !is_file($path)) {
            throw new MapperException('No short message file found');
        }
        $this->record->getMapper()->setFile($path);
    }

    public function getRecord(): ShortMessage
    {
        return $this->record;
    }
}
