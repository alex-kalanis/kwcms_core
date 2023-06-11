<?php

namespace KWCMS\modules\Rss\Lib;


use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_mapper\Mappers;


/**
 * Class ShortMessageMapper
 * @package KWCMS\modules\Rss\Lib
 */
class ShortMessageMapper extends Mappers\File\ATable
{
    protected function setMap(): void
    {
        $this->setFormat(SeparatedElements::class);
        $this->orderFromFirst(false);
        $this->setRelation('id', 0);
        $this->setRelation('date', 1);
        $this->setRelation('title', 2);
        $this->setRelation('content', 3);
        $this->addPrimaryKey('id');
    }

    public function setAccessing(IProcessFiles $files): void
    {
        $this->setFileAccessor($files);
    }
}
