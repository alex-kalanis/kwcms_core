<?php

namespace kalanis\kw_table\core\Table\Columns;


use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class CStatic
 * @package kalanis\kw_table\core\Table\Columns
 * Static value defined on init
 */
class CStatic extends AColumn
{
    private $value = '';
    private $class = '';

    public function __construct(string $value, string $class = '', string $sourceName = '')
    {
        $this->value = $value;
        $this->class = $class;
        $this->sourceName = $sourceName;
    }

    public function getValue(IRow $source)
    {
        if (!empty($this->class)) return $this->returnWithClass();
        return $this->value;
    }

    private function returnWithClass()
    {
        return '<span class="' . $this->class . '">' . $this->value . '</span>';
    }
}
