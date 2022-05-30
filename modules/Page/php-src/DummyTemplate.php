<?php

namespace KWCMS\modules\Page;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class DummyTemplate
 * @package KWCMS\modules\Page
 */
class DummyTemplate extends ATemplate
{
    protected function loadTemplate(): string
    {
        return '';
    }

    protected function fillInputs(): void
    {
    }

    public function setData(string $content): self
    {
        $this->setTemplate($content);
        return $this;
    }
}
