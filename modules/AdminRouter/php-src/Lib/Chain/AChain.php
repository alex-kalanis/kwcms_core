<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_input\Entries\Entry;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_paths\Path;


/**
 * Class AChain
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes
 */
abstract class AChain
{
    /** @var AChain|null */
    protected $next = null;
    /** @var IVariables */
    protected $inputs = null;
    /** @var IModule|null */
    protected $module = null;
    /** @var ILoader */
    protected $loader = null;
    /** @var Path */
    protected $path = null;
    /** @var string[]|Entry[] */
    protected $params = [];
    /** @var int */
    protected $keyLevel = 0;

    public function __construct(ILoader $loader, Path $path, int $keyLevel = ISitePart::SITE_ROUTED)
    {
        $this->loader = $loader;
        $this->path = $path;
        $this->keyLevel = $keyLevel;
    }

    public function init(IVariables $inputs, array $passedParams): self
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

    /**
     * @return IModule
     * @throws ModuleException
     */
    abstract public function getModule(): IModule;

    /**
     * @param string $name
     * @param string|null $pathToController
     * @return IModule
     * @throws ModuleException
     */
    protected function moduleInit(string $name, ?string $pathToController): IModule
    {
        $module = $this->loader->load($name, $pathToController);
        if (!$module) {
            throw new ModuleException(sprintf('Controller for wanted module *%s* not found!', $name));
        }
        $module->init($this->inputs, array_merge(
            $this->params, [ISitePart::KEY_LEVEL => $this->keyLevel]
        ));
        return $module;
    }
}
