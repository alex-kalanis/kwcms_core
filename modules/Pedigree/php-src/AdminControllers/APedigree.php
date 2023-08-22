<?php

namespace KWCMS\modules\Pedigree\AdminControllers;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth_sources\Interfaces\IWorkClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_pedigree\Storage;
use KWCMS\modules\Pedigree\Lib;


/**
 * Class APedigree
 * @package KWCMS\modules\Pedigree\AdminControllers
 * Site's Pedigree - basics
 */
abstract class APedigree extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;

    /** @var MapperException|null */
    protected $error = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var Forward */
    protected $forward = null;

    /**
     * @throws ConfException
     * @throws LangException
     */
    public function __construct()
    {
        Config::load('Pedigree');
        $this->initTModuleTemplate();
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [IWorkClasses::CLASS_MAINTAINER, IWorkClasses::CLASS_ADMIN, IWorkClasses::CLASS_USER, ];
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
