<?php

namespace kalanis\kw_connect\search;


use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class Row
 * @package kalanis\kw_connect\search
 */
class Row implements IRow
{
    protected ARecord $record;

    public function __construct(ARecord $record)
    {
        $this->record = $record;
    }

    public function getValue($property)
    {
        if (method_exists($this->record, strval($property))) {
            return call_user_func([$this->record, strval($property)]);
        } else {
            return $this->record->__get($property);
        }
    }

    public function __isset($name)
    {
        return $this->record->offsetExists($name);
    }
}
