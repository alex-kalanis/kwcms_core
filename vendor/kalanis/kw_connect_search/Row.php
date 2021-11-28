<?php

namespace kalanis\kw_connect_search;


use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class Row
 * @package kalanis\kw_connect_search
 */
class Row implements IRow
{
    protected $record;

    public function __construct(ARecord $record)
    {
        $this->record = $record;
    }

    public function getValue($property)
    {
        if (method_exists($this->record, $property)) {
            return call_user_func([$this->record, $property]);
        } else {
            return $this->record->__get($property);
        }
    }

    public function __isset($name)
    {
        return $this->record->offsetExists($name);
    }
}