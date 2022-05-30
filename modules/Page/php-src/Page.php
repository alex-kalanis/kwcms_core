<?php

namespace KWCMS\modules\Page;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\InternalLink;
use kalanis\kw_modules\Loaders\KwLoader;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_modules\Processing\FileProcessor;
use kalanis\kw_modules\Processing\ModuleRecord;
use kalanis\kw_modules\Processing\Modules;
use kalanis\kw_modules\SubModules;


/**
 * Class Page
 * @package KWCMS\modules\Page
 * Page's content
 * What to sent back as HTML code - page content itself
 * - parse default page managed by user into blocks and load content for that blocks
 */
class Page extends AModule
{
    /** @var SubModules */
    protected $subModules = null;
    /** @var InternalLink */
    protected $link = null;
    /** @var string */
    protected $content = '';

    public function __construct(?ILoader $loader = null, ?Modules $processor = null)
    {
        Config::load('Core', 'page');
        $loader = $loader ?: new KwLoader();
        $moduleProcessor = $processor ?: new Modules(new FileProcessor(new ModuleRecord(), Config::getPath()->getDocumentRoot() . Config::getPath()->getPathToSystemRoot() ));
        $moduleProcessor->setLevel(ISitePart::SITE_CONTENT);
        $this->subModules = new SubModules($loader, $moduleProcessor);
        $this->link = new InternalLink(Config::getPath());
    }

    public function process(): void
    {
        $path = $this->link->userContent();
        if (empty($path) || is_dir($path)) {
            $path = $this->link->userContent('index.htm', false, false);
        }
        $content = @file_get_contents($path);
        if (false === $content) {
            throw new ModuleException(sprintf('Cannot load content on path *%s*', Config::getPath()->getPath()));
        }
        $this->content = strval($content);
    }

    public function output(): AOutput
    {
        $template = new DummyTemplate();
        $template->setData($this->content);
        $this->subModules->fill($template, $this->inputs, ISitePart::SITE_CONTENT, $this->params);
        $out = new Html();
        return $out->setContent($template->render());
    }
}
