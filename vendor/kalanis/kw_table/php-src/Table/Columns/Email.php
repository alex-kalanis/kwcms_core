<?php

namespace kalanis\kw_table\Table\Columns;


use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class Email
 * @package kalanis\kw_table\Table\Columns
 * Column contains an email, so make a link
 */
class Email extends AColumn
{
    protected $format = '';

    public function __construct(string $sourceName)
    {
        $this->sourceName = $sourceName;
    }

    public function getValue(IRow $source)
    {
        return '<a href="mailto:' . parent::getValue($source) . '">' . parent::getValue($source) . '</a>';
    }
}
