<?php

namespace KWCMS\modules\Chapters\Lib;


use KWCMS\modules\Core\Libs\ATemplate;
use kalanis\kw_templates\TemplateException;


/**
 * Class PageTemplate
 * @package KWCMS\modules\Chapters\Lib
# style of page -> which template and which style may be shown
 */
class PageTemplate extends ATemplate
{
    protected string $moduleName = 'Chapters';
    protected string $templateName = 'actual_page';

    /* Which styles are available and if they want solo rows */
    protected static $styles = [ # usage of one line - one file (for count cols)
        'prev_page',
        'actual_page',
        'next_page',
    ];

    /**
     * @param string $name
     * @throws TemplateException
     * @return PageTemplate
     */
    public function setTemplateName(string $name): self
    {
        if (in_array($name, static::$styles)) {
            $this->templateName = $name;
            $this->setTemplate($this->loadTemplate());
        }
        return $this;
    }

    protected function fillInputs(): void
    {
        $this->addInput('{PAGE}');
        $this->addInput('{LINK}');
    }

    public function setData(int $page, string $link): self
    {
        $this->updateItem('{PAGE}', $page);
        $this->updateItem('{LINK}', $link);
        return $this;
    }
}
