<?php

namespace KWCMS\modules\Short;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;
use kalanis\kw_short\ShortException;
use kalanis\kw_table\TableException;
use KWCMS\modules\Admin\Shared;


/**
 * Class Dashboard
 * @package KWCMS\modules\Short
 * Site's short messages - admin table
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;

    /** @var Search|null */
    protected $search = null;
    /** @var MapperException|null */
    protected $error = null;

    public function __construct()
    {
        Config::load('Short');
        $this->initTModuleTemplate();
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $adapter = new Lib\MessageAdapter($this->inputs, Config::getPath());
            $this->search = new Search($adapter->getRecord());
        } catch (MapperException | ShortException $ex) {
            $this->error = $ex;
        }
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
        $table = new Lib\MessageTable($this->inputs);
        if ($this->search) {
            try {
                return $out->setContent($this->outModuleTemplate($table->prepareHtml($this->search)));
            } catch (MapperException | TableException | FormsException $ex) {
                $this->error = $ex;
            }
        }

        if ($this->error) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage()));
        } else {
            return $out->setContent($this->outModuleTemplate(Lang::get('short.cannot_read')));
        }
    }

    public function outJson(): Output\AOutput
    {
        $out = new Output\Json();
        $table = new Lib\MessageTable($this->inputs);
        try {
            if ($this->search) {
                return $out->setContent($table->prepareJson($this->search));
            }
            $this->error = new ModuleException('No table found in current directory');
        } catch (MapperException | TableException | FormsException $ex) {
            $this->error = $ex;
        }

        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            return $out->setContent(Lang::get('short.cannot_read'));
        }
    }

    public function getTitle(): string
    {
        return Lang::get('short.page');
    }
}
