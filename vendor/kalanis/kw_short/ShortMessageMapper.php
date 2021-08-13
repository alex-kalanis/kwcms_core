<?php

namespace kalanis\kw_short;


use kalanis\kw_mapper\Mappers;


/**
 * Class ShortMessageMapper
 * @package kalanis\kw_short
 */
class ShortMessageMapper extends Mappers\File\ATable
{
    protected function setMap(): void
    {
        $this->setFormat('\kalanis\kw_mapper\Storage\File\Formats\SeparatedElements');
        $this->orderFromFirst(false);
        $this->setRelation('id', 0);
        $this->setRelation('date', 1);
        $this->setRelation('title', 2);
        $this->setRelation('content', 3);
        $this->addPrimaryKey('id');
    }
}
