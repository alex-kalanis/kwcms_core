<?php

namespace KWCMS\modules\Core\Libs;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_modules\Interfaces\IModule;


/**
 * Class AModule
 * @package KWCMS\modules\Core\Libs
 * Basic class for each module
 *
 * __construct() is for DI/class building
 * process() is for that hard work
 * output() is for getting output class/data
 */
abstract class AModule implements IModule
{
    /** @var IFiltered|null */
    protected $inputs = null;
    /** @var array<int|string, bool|float|int|string|array<int|string>> */
    protected $params = [];

    // enable if you do not use DI and want just pass params into modules
//    abstract public function __construct(...$constructParams);

    public function init(IFiltered $inputs, array $passedParams): void
    {
        $this->inputs = $inputs;
        $this->params = $passedParams;
    }

    public static function getClassName(string $class): string
    {
        $classParts = explode('\\', $class);
        return end($classParts);
    }

    protected function isJson(): bool
    {
        if ($this->inputs) {
            $json = $this->inputs->getInArray('json', [IEntry::SOURCE_CLI, IEntry::SOURCE_POST, IEntry::SOURCE_GET]);
            return !empty($json);
        }
        return false;
    }

    protected function isRaw(): bool
    {
        if ($this->inputs) {
            $raw = $this->inputs->getInArray('raw', [IEntry::SOURCE_CLI, IEntry::SOURCE_POST, IEntry::SOURCE_GET]);
            return !empty($raw);
        }
        return false;
    }

    /**
     * @param string $key
     * @param bool|float|int|string|array<int|string>|null $default
     * @return bool|float|int|string|array<int|string>|null
     */
    protected function getFromParam(string $key, $default = null)
    {
        return isset($this->params[$key]) ? (is_object($this->params[$key]) ? $this->params[$key]->getValue() : $this->params[$key]) : $default ;
    }
}
