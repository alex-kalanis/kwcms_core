<?php

namespace KWCMS\modules\Krep\Controllers;


use kalanis\kw_modules\Output;
use KWCMS\modules\Krep\Libs;
use KWCMS\modules\Core\Libs\AModule;


/**
 * Class ADisposition
 * @package KWCMS\modules\Krep\Controllers
 */
abstract class ADisposition extends AModule
{
    protected Libs\Template $defaultTemplate;

    public function __construct(
        protected readonly Libs\Config $config,
    )
    {
        $this->defaultTemplate = new Libs\Template('head');
    }

    public function process(): void
    {
    }

    public function output(): Output\AOutput
    {
        $this->defaultTemplate->change('{NAME}', $this->config->site_name);
        $this->defaultTemplate->change('{CONTENT}', $this->getContent());
        $this->defaultTemplate->change('{TITLE}', $this->config->site_name);
        return (new Output\Html())->setContent($this->defaultTemplate->render());
    }

    abstract protected function getContent(): string;
}
