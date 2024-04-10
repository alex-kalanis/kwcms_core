<?php

namespace KWCMS\modules\Pedigree\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\Output;
use kalanis\kw_pedigree\PedigreeException;
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
    use Lib\TCorrectConnect;
    use Lib\TModuleTemplate;

    /** @var MapperException|null */
    protected $error = null;
    protected bool $isProcessed = false;
    protected Forward $forward;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws LangException
     * @throws PedigreeException
     * @throws HandlerException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Pedigree');
        $this->initTModuleTemplate();
        \kalanis\kw_pedigree\Config::init();
        $this->initTCorrectConnect($constructParams);
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
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
