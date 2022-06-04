<?php

namespace KWCMS\modules\Pedigree;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_pedigree\Storage;


/**
 * Class APedigree
 * @package KWCMS\modules\Pedigree
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

    public function __construct()
    {
        Config::load('Pedigree');
        $this->initTModuleTemplate();
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
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
