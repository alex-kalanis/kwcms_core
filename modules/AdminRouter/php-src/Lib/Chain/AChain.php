<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_input\Entries\Entry;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\RoutedPath;
use kalanis\kw_routed_paths\Support;


/**
 * Class AChain
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes
 */
abstract class AChain
{
    protected ?AChain $next = null;
    protected ?IFiltered $inputs = null;
    protected ?IModule $module = null;
    protected ILoader $loader;
    protected RoutedPath $path;
    /** @var string[]|Entry[] */
    protected array $params = [];
    protected int $keyLevel = 0;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected array $constructParams = [];

    /**
     * AChain constructor.
     * @param ILoader $loader
     * @param RoutedPath $path
     * @param int $keyLevel
     * @param array $constructParams
     */
    public function __construct(ILoader $loader, RoutedPath $path, int $keyLevel = ISitePart::SITE_ROUTED, array $constructParams = [])
    {
        $this->loader = $loader;
        $this->path = $path;
        $this->keyLevel = $keyLevel;
        $this->constructParams = $constructParams;
    }

    public function init(IFiltered $inputs, array $passedParams): self
    {
        $this->inputs = $inputs;
        $this->params = $passedParams;
        return $this;
    }

    public function setNext(?AChain $chain): self
    {
        $this->next = $chain;
        return $this;
    }

    public function getNext(): ?AChain
    {
        return $this->next;
    }

    protected function getModulePath(): array
    {
        return array_map(
            'ucfirst',
            array_map(
                [Support::class, 'normalizeModuleName'],
                array_map(
                    'strtolower',
                    $this->path->getPath()
                )
            )
        );
    }

    /**
     * @return IModule
     * @throws ModuleException
     */
    abstract public function getModule(): IModule;

    /**
     * @param string[] $path
     * @throws PathsException
     * @throws ModuleException
     * @return IModule
     */
    protected function moduleInit(array $path): IModule
    {
        $module = $this->loader->load($path, $this->constructParams);
        if (!$module) {
            throw new ModuleException(sprintf('Controller for wanted module *%s* not found!', Stuff::arrayToPath($path)));
        }
        $module->init($this->inputs, array_merge(
            $this->params, [ISitePart::KEY_LEVEL => $this->keyLevel]
        ));
        return $module;
    }
}
