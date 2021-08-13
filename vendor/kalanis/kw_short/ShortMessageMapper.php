<?php

namespace kalanis\kw_short;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Extras\SessionAdapter;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers;
use kalanis\kw_tree\TWhereDir;


/**
 * Class ShortMessageMapper
 * @package kalanis\kw_short
 */
class ShortMessageMapper extends Mappers\File\ATable
{
    use TWhereDir;

    protected $dirPath = '';

    public function __construct()
    {
        // need to pass currently selected directory
        // na drevaka...
        $path = Config::getPath();
        $this->initWhereDir(new SessionAdapter(), null);
        $this->dirPath = $path->getDocumentRoot() . $path->getPathToSystemRoot() . $this->getWhereDir();
        parent::__construct();
    }

    protected function setMap(): void
    {
        $path = realpath($this->dirPath . DIRECTORY_SEPARATOR
            . Config::get('Short', 'name', 'index')
            . Config::get('Short', 'suff', '.short')
        );
        if (false === $path || !is_file($path)) {
            throw new MapperException('No short message file found');
        }
        $this->setFile($path);
        $this->setFormat('\kalanis\kw_mapper\Storage\File\Formats\SeparatedElements');
        $this->orderFromFirst(false);
        $this->setRelation('id', 0);
        $this->setRelation('date', 1);
        $this->setRelation('title', 2);
        $this->setRelation('content', 3);
        $this->addPrimaryKey('id');
    }
}
