<?php

namespace KWCMS\modules\Pedigree\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\Output;
use kalanis\kw_pedigree\Storage;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Pedigree\Lib;


/**
 * Class APedigree
 * @package KWCMS\modules\Pedigree\AdminControllers
 * Site's Pedigree - basics
 */
abstract class APedigree extends AAuthModule implements IHasTitle
{
    use Lib\TModuleTemplate;

    /** @var MapperException|null */
    protected $error = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var Forward */
    protected $forward = null;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Pedigree');
        $this->initTModuleTemplate();
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    protected function getRecord(): ARecord
    {
        \kalanis\kw_pedigree\Config::init();
        return new Storage\SingleTable\PedigreeRecord();
//        return new Storage\MultiTable\PedigreeItemRecord();
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    abstract protected function outHtml(): Output\AOutput;

    abstract protected function outJson(): Output\AOutput;
}
