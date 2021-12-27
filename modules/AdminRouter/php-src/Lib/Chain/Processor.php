<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_input\Entries\Entry;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_notify\Notification;
use Throwable;


/**
 * Class Processor
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes - main processor
 */
class Processor
{
    /** @var AChain|null */
    protected $first = null;
    /** @var AChain|null */
    protected $last = null;
    /** @var IVariables */
    protected $inputs = null;
    /** @var string[]|Entry[] */
    protected $params = [];

    public function init(IVariables $inputs, array $passedParams): self
    {
        $this->inputs = $inputs;
        $this->params = $passedParams;
        return $this;
    }

    public function addToChain(AChain $chain): self
    {
        if ($this->last) {
            $this->last->setNext($chain);
        } else {
            $this->first = $chain;
        }
        $this->last = $chain;
        return $this;
    }

    /**
     * @return IModule
     * @throws ModuleException
     */
    public function process(): IModule
    {
        if (!$this->first) {
            throw new ModuleException('No start point for lookup!');
        }
        $chain = $this->first;
        while ($chain) {
            try {
                $chain->init($this->inputs, $this->params);
                return $chain->getModule();
            } catch (Throwable $ex) {
                if (!$chain->getNext()) {
                    Notification::addError($ex->getMessage());
                }
            }
            $chain = $chain->getNext();
        }
        throw new ModuleException('No available module passed!');
    }
}
