<?php

namespace kalanis\kw_table_output_direct;


use kalanis\kw_table\Table;


/**
 * Class DirectRenderer
 * @package kalanis\kw_table_output_direct
 * Direct renderer into PHP template
 */
class DirectRenderer extends Table\AOutput
{
    protected $template = null;

    public function __construct(Table $table)
    {
        parent::__construct($table);
        $this->template = new Template();
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function render(): string
    {
        return $this->template->render($this->table);
    }
}
