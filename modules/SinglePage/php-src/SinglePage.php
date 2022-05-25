<?php

namespace KWCMS\modules\SinglePage;


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
 * Class SinglePage
 * @package KWCMS\modules\Page
 * Single Page's content
 * What to sent back as HTML code - page content itself
 * - parse default page managed by user into blocks and load content for that blocks
 */
class SinglePage extends AModule
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
        $path = Config::getPath();
        $moduleProcessor = $processor ?: new Modules(new FileProcessor(new ModuleRecord(), $path->getDocumentRoot() . $path->getPathToSystemRoot() ));
        $this->subModules = new SubModules($loader, $moduleProcessor);
        $this->link = new InternalLink(Config::getPath());
    }

    public function process(): void
    {
        $configPath = Config::get('Core', 'singlePage', 'index.htm');
        $path = $this->link->userContent($configPath, false, false);
        $content = @file_get_contents($path);
        if (false === $content) {
            throw new ModuleException(sprintf('Cannot load content on path *%s*', $configPath));
        }
        $this->content = strval($content);
    }

    public function output(): AOutput
    {
        $template = new PageTemplate();
        $template->setData($this->content);
        // add modules which fill the layout
        $this->subModules->fill($template, $this->inputs, ISitePart::SITE_LAYOUT, $this->params);
        // add modules which fill the content
        $this->subModules->fill($template, $this->inputs, ISitePart::SITE_CONTENT, $this->params);
        $out = new Html();
        return $out->setContent($template->render());
    }
}
