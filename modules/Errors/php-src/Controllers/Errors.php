<?php

namespace KWCMS\modules\Errors\Controllers;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\JsonError;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Errors\Lib;


/**
 * Class Errors
 * @package KWCMS\modules\Errors\Controllers
 * Error sign as page filler
 */
class Errors extends AModule
{
    protected int $code = 0;
    protected string $desc = '';

    /** @var int[] */
    protected static array $acceptable_errors = [400,401,403,404,405,406,407,408,409,410,411,413,414,415,500,501,502,503,504,505];

    /**
     * @param mixed ...$constructParams
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        Lang::load(static::getClassName(static::class));
    }

    public function process(): void
    {
        $codeFromErr = intval(strval($this->getFromInput('err', 0, [IEntry::SOURCE_EXTERNAL, IEntry::SOURCE_GET])));
        $codeFromError = intval(strval($this->getFromInput('error', 0, [IEntry::SOURCE_EXTERNAL, IEntry::SOURCE_GET])));
        $codeFromPassed = intval(strval($this->getFromParam('error', 0)));
        $descFromPassed = strval($this->getFromParam('error_message', ''));

        if (!empty($codeFromErr)) {
            $code = $codeFromErr;
        } elseif (!empty($codesFromError)) {
            $code = $codeFromError;
        } elseif (!empty($codeFromPassed)) {
            $code = $codeFromPassed;
            $this->desc = $descFromPassed;
        } else {
            $arrPt = new ArrayPath();
            $code = Stuff::fileBase($arrPt->setArray(StoreRouted::getPath()->getPath())->getFileName());
        }

        $this->code = in_array((int)$code, static::$acceptable_errors) ? $code : 403 ;
    }

    public function output(): AOutput
    {
        if ($this->isJson()) {
            $out = new JsonError();
            return $out->setContent($this->code, Lang::get('error.desc.' . $this->code));
        } else {
            $out = new Lib\OutHtml();
            return $out->setContent(strval($this->code), $this->desc);
        }
    }
}
