<?php

namespace KWCMS\modules\Errors;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\JsonError;
use kalanis\kw_paths\Stuff;


/**
 * Class Errors
 * @package KWCMS\modules\Errors
 * Error sign as page filler
 */
class Errors extends AModule
{
    /** @var AOutput */
    protected $out = null;
    /** @var string */
    protected $code = '';

    protected static $acceptable_errors = ["400","401","403","404","405","406","407","408","409","410","411","413","414","415","500","501","502","503","504","505"];

    public function __construct()
    {
        Lang::load(static::getClassName(static::class));
    }

    public function process(): void
    {
        $codesFromErr = $this->inputs->getInArray('err', [IEntry::SOURCE_EXTERNAL, IEntry::SOURCE_GET]);
        $codesFromError = $this->inputs->getInArray('error', [IEntry::SOURCE_EXTERNAL, IEntry::SOURCE_GET]);

        if (!empty($codesFromErr)) {
            $code = reset($codesFromErr);
        } elseif (!empty($codesFromError)) {
            $code = reset($codesFromError);
        } else {
            $code = Stuff::fileBase(Stuff::filename(Config::getPath()->getPath()));
        }

        $this->code = in_array($code, static::$acceptable_errors) ? $code : '403' ;
    }

    public function output(): AOutput
    {
        if ($this->isJson()) {
            $out = new JsonError();
            return $out->setContent($this->code, Lang::get('error.desc.' . $this->code));
        } else {
            $out = new Lib\OutHtml();
            return $out->setContent($this->code);
        }
    }
}
