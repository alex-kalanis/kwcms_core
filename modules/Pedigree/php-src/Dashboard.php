<?php

namespace KWCMS\modules\Pedigree;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage;
use kalanis\kw_table\TableException;
use KWCMS\modules\Admin\Shared;


/**
 * Class Dashboard
 * @package KWCMS\modules\Pedigree
 * Site's Pedigree - admin table
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;

    /** @var GetEntries|null */
    protected $entries = null;
    /** @var MapperException|null */
    protected $error = null;

    public function __construct()
    {
        Config::load('Pedigree');
        $this->initTModuleTemplate();
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->entries = new GetEntries($this->getRecord());
        } catch (PedigreeException $ex) {
            $this->error = $ex;
        }
    }

    protected function getRecord(): ARecord
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
        $out = new Shared\FillHtml($this->user);
        $table = new Lib\PedigreeTable($this->inputs, $this->links, $this->entries);
        try {
            return $out->setContent($this->outModuleTemplate($table->prepareHtml()));
        } catch (MapperException | TableException | FormsException | \PDOException $ex) {
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
        } catch (MapperException | TableException | FormsException $ex) {
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
