<?php

namespace KWCMS\modules\SinglePage\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_modules\Access\Factory as modules_factory;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Interfaces\Lists\IModulesList;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Mixer\Processor;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
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
    use TToString;

    /** @var ILoader */
    protected $loader = null;
    /** @var IModule|null */
    protected $module = null;
    /** @var IModulesList */
    protected $modulesList = null;
    /** @var Processor */
    protected $subModules = null;
    /** @var string */
    protected $content = '';
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var InnerLinks */
    protected $innerLink = null;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws ModuleException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Core', 'page');

        $this->constructParams = $constructParams;
        // this part is about module loader, it depends one each server
        $modulesFactory = new modules_factory();
        $this->loader = $modulesFactory->getLoader(['modules_loaders' => [$constructParams, 'web']]);
        $this->modulesList = $modulesFactory->getModulesList($constructParams);
        $this->subModules = new Processor($this->loader, $this->modulesList);

        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'site.more_lang', false))
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
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
        $this->content = $this->toString(Stuff::arrayToPath($path), $this->files->readFile($path));
    }

    public function output(): AOutput
    {
        $out = new Html();

        $body = new PageTemplate();
        $bodyToReplace = $body->reset()->get();
        // add modules which fill the layout
        $bodyUpdated = $this->subModules->fill($bodyToReplace, $this->inputs, ISitePart::SITE_LAYOUT, $this->params, $this->constructParams);
        // add modules which fill the content
        $bodyUpdated = $this->subModules->fill($bodyUpdated, $this->inputs, ISitePart::SITE_CONTENT, $this->params, $this->constructParams);
        $body->change($bodyToReplace, $bodyUpdated);

        return $out->setContent($body->setData($this->content)->render());
    }
}
