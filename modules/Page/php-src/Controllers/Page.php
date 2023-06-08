<?php

namespace KWCMS\modules\Page\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\ISitePart;
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
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Page\DummyTemplate;


/**
 * Class Page
 * @package KWCMS\modules\Page\Controllers
 * Page's content
 * What to sent back as HTML code - page content itself
 * - parse default page managed by user into blocks and load content for that blocks
 */
class Page extends AModule
{
    use TToString;

    /** @var SubModules */
    protected $subModules = null;
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
     * @throws FilesException
     * @throws PathsException
     * @throws ConfException
     */
    public function __construct(?ILoader $loader = null, ?Modules $processor = null)
    {
        Config::load('Core', 'page');
        $loader = $loader ?: new KwLoader();
        $moduleProcessor = $processor ?: new Modules(new FileProcessor(new ModuleRecord(), Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot() ));
        $moduleProcessor->setLevel(ISitePart::SITE_CONTENT);
        $this->subModules = new SubModules($loader, $moduleProcessor);
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            false
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
        $userPath = $this->innerLink->toFullPath(StoreRouted::getPath()->getPath());
        $content = null;
        if ($this->files->isFile($userPath)) {
            $content = $this->toString(Stuff::arrayToPath($userPath), $this->files->readFile($userPath));
        }

        $indexPath = array_merge($userPath, ['index.htm']);
        if (!$content && $this->files->isDir($userPath) && $this->files->isFile($indexPath)) {
            $content = $this->toString(Stuff::arrayToPath($indexPath), $this->files->readFile($indexPath));
        }

        $indexPath = array_merge($userPath, ['index.html']);
        if (!$content && $this->files->isDir($userPath) && $this->files->isFile($indexPath)) {
            $content = $this->toString(Stuff::arrayToPath($indexPath), $this->files->readFile($indexPath));
        }

        $indexPath = array_merge($userPath, ['default.html']);
        if (!$content && $this->files->isDir($userPath) && $this->files->isFile($indexPath)) {
            $content = $this->toString(Stuff::arrayToPath($indexPath), $this->files->readFile($indexPath));
        }

        if (!$content) {
            throw new ModuleException(sprintf('Cannot load content on path *%s*', Stuff::arrayToPath(StoreRouted::getPath()->getPath())), 404);
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
