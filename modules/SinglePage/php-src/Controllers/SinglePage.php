<?php

namespace KWCMS\modules\SinglePage\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
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
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\SinglePage\PageTemplate;


/**
 * Class SinglePage
 * @package KWCMS\modules\SinglePage\Controllers
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
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var InnerLinks */
    protected $innerLink = null;

    /**
     * @param ILoader|null $loader
     * @param Modules|null $processor
     * @throws ConfException
     * @throws FilesException
     * @throws PathsException
     */
    public function __construct(?ILoader $loader = null, ?Modules $processor = null)
    {
        Config::load('Core', 'page');
        $loader = $loader ?: new KwLoader();
        $path = Stored::getPath();
        $moduleProcessor = $processor ?: new Modules(new FileProcessor(new ModuleRecord(), $path->getDocumentRoot() . $path->getPathToSystemRoot() ));
        $this->subModules = new SubModules($loader, $moduleProcessor);
        $this->link = new InternalLink(Stored::getPath(), StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'site.more_lang', false))
        );
        $this->files = (new Factory())->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
    }

    /**
     * @throws FilesException
     * @throws ModuleException
     * @throws PathsException
     */
    public function process(): void
    {
        $configPath = Config::get('Core', 'singlePage', 'index.htm');
        $path = $this->innerLink->toFullPath(
            $this->arrPath->setString($configPath)->getArray()
        );
        if (!$this->files->isFile($path)) {
            throw new ModuleException(sprintf('Cannot load content on path *%s*', $configPath));
        }
        $this->content = $this->files->readFile($path);
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
