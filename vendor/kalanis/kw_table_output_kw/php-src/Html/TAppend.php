<?php

namespace kalanis\kw_table_output_html\Html;


trait TAppend
{
    protected function appendContent(string $key, string $content): void
    {
        $this->items[$key]->setValue( $this->items[$key]->getValue() . $content );
    }
}
