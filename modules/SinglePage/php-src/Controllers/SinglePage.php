<?php

namespace KWCMS\modules\SinglePage\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_langs\LangException;
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
use KWCMS\modules\Errors\Controllers\Errors;
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

    protected ILoader $loader;
    /** @var IModule|null */
    protected ?IModule $module = null;
    protected IModulesList $modulesList;
    protected Processor $subModules;
    protected string $content = '';
    protected ArrayPath $arrPath;
    protected CompositeAdapter $files;
    protected InnerLinks $innerLink;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected array $constructParams = [];
    protected bool $dumpImmediately = false;

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
            boolval(Config::get('Core', 'site.more_lang', false)),
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
        $this->dumpImmediately = boolval(intval(Config::get('Core', 'site.debug', false)));
    }

    /**
     * @throws FilesException
     * @throws LangException
     * @throws ModuleException
     * @throws PathsException
     */
    public function process(): void
    {
        try {
            $configPath = Config::get('Core', 'singlePage', 'index.htm');
            $path = $this->innerLink->toFullPath(
                $this->arrPath->setString($configPath)->getArray()
            );
            if (!$this->files->isFile($path)) {
                throw new ModuleException(sprintf('Cannot load content on path *%s*', $configPath), 404);
            }
            $this->content = $this->toString(Stuff::arrayToPath($path), $this->files->readFile($path));
        } catch (ModuleException $ex) {
            if ($this->dumpImmediately) {
                throw $ex;
            }
            $module = new Errors($this->constructParams);
            $module->init($this->inputs, array_merge(
                $this->params, [
                    ISitePart::KEY_LEVEL => ISitePart::SITE_LAYOUT,
                    'error' => $ex->getCode() ?: 500,
                    'error_message' => $ex->getMessage()
                ]
            ));
            $module->process();
            $this->content = $module->output()->output();
        }
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
