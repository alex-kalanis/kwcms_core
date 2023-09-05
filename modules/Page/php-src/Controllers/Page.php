<?php

namespace KWCMS\modules\Page\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_modules\Access\Factory as modules_factory;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Mixer\Processor;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\FilesTranslations;


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

    /** @var Processor */
    protected $subModules = null;
    /** @var string */
    protected $content = '';
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var InnerLinks */
    protected $innerLink = null;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws ModuleException
     * @throws PathsException
     * @throws ConfException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Core', 'page');

        $this->constructParams = $constructParams;
        // this part is about module loader, it depends one each server
        $modulesFactory = new modules_factory();
        $loader = $modulesFactory->getLoader(['modules_loaders' => [$constructParams, 'web']]);
        $moduleProcessor = $modulesFactory->getModulesList($constructParams);
        $moduleProcessor->setModuleLevel(ISitePart::SITE_CONTENT);
        $this->subModules = new Processor($loader, $moduleProcessor);

        // this part is on possible remote storage, so it must be set separately
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            false
        );
        $this->files = (new files_factory(new FilesTranslations()))->getClass($constructParams);
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
        return (new Html())->setContent($this->subModules->fill($this->content, $this->inputs, ISitePart::SITE_CONTENT, $this->params, $this->constructParams));
    }
}
