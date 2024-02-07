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
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected $constructParams = [];
    /** @var Libs\Template */
    protected $defaultTemplate = null;
    /** @var Libs\Config */
    protected $config;

    public function __construct(Libs\Config $config)
    {
        $this->config = $config;
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
