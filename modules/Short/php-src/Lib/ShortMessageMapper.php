<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_mapper\Mappers;
use kalanis\kw_mapper\Storage\Shared\FormatFiles;


/**
 * Class ShortMessageMapper
 * @package KWCMS\modules\Short\Lib
 */
class ShortMessageMapper extends Mappers\File\ATable
{
    protected function setMap(): void
    {
        $this->setFormat(FormatFiles\SeparatedElements::class);
        $this->orderFromFirst(false);
        $this->setRelation('id', 0);
        $this->setRelation('date', 1);
        $this->setRelation('title', 2);
        $this->setRelation('content', 3);
        $this->addPrimaryKey('id');
    }
}
