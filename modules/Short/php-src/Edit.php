<?php

namespace KWCMS\modules\Short;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\Adapters\DataExchange;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_short\ShortException;
use KWCMS\modules\Admin\Shared;


/**
 * Class Edit
 * @package KWCMS\modules\Short
 * Site's short messages - edit form
 */
class Edit extends AAuthModule implements IModuleTitle
{
    /** @var Lib\MessageForm|null */
    protected $form = null;
    /** @var MapperException|null */
    protected $error = null;

    public function __construct()
    {
        Config::load('Short');
        Lang::load('Short');
        $this->form = new Lib\MessageForm('editMessage');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $adapter = new Lib\MessageAdapter($this->inputs, Config::getPath());
            $record = $adapter->getRecord();
            $record->id = strval($this->getFromParam('id'));
            $record->load();
            $this->form->composeForm($record);
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $ex = new DataExchange($record);
                $ex->import($this->form->getValues());
                $record->save();
                Notification::addSuccess(Lang::get('short.updated'));
            }
        } catch (MapperException | FormsException | ShortException $ex) {
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
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            }
            return $out->setContent($this->form->render());
        } catch (FormsException $ex) {
            return $out->setContent($this->error->getMessage());
        }
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } elseif (!$this->form->isValid()) {
            $out = new Output\JsonError();
            return $out->setContent(1, $this->form->renderErrorsArray());
        } else {
            $out = new Output\Json();
            return $out->setContent(['Success']);
        }
    }

    public function getTitle(): string
    {
        return Lang::get('short.page') . ' - ' . Lang::get('short.update_message');
    }
}
