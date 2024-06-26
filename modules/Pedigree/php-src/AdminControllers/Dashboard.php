<?php

namespace KWCMS\modules\Pedigree\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\Output;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage;
use kalanis\kw_table\core\TableException;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Pedigree\Lib;


/**
 * Class Dashboard
 * @package KWCMS\modules\Pedigree\AdminControllers
 * Site's Pedigree - admin table
 */
class Dashboard extends AAuthModule implements IHasTitle
{
    use Lib\TModuleTemplate;

    /** @var GetEntries */
    protected ?GetEntries $entries = null;
    /** @var MapperException|ConnectException|null */
    protected $error = null;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Pedigree');
        $this->initTModuleTemplate();
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->entries = new GetEntries($this->getRecord());
        } catch (PedigreeException $ex) {
            $this->error = $ex;
        }
    }

    protected function getRecord(): Storage\APedigreeRecord
    {
        return new Storage\SingleTable\PedigreeRecord();
//        return new Storage\MultiTable\PedigreeItemRecord();
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        $table = new Lib\PedigreeTable($this->inputs, $this->links, $this->entries);
        try {
            return $out->setContent($this->outModuleTemplate($table->prepareHtml()));
        } catch (ConnectException | FormsException | LangException | MapperException | TableException $ex) {
            $this->error = $ex;
        }

        if ($this->error) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        } else {
            return $out->setContent($this->outModuleTemplate(Lang::get('pedigree.cannot_read')));
        }
    }

    public function outJson(): Output\AOutput
    {
        $out = new Output\Json();
        $table = new Lib\PedigreeTable($this->inputs, $this->links, $this->entries);
        try {
            return $out->setContent($table->prepareJson());
        } catch (ConnectException | FormsException | MapperException | TableException $ex) {
            $this->error = $ex;
        }

        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            return $out->setContent(Lang::get('pedigree.cannot_read'));
        }
    }

    public function getTitle(): string
    {
        return Lang::get('pedigree.page');
    }
}
