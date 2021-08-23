<?php

namespace KWCMS\modules\Short;


use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\Forward;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\Adapters\DataExchange;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use KWCMS\modules\Admin\Shared;


/**
 * Class Add
 * @package KWCMS\modules\Short
 * Site's short messages - add form
 */
class Add extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;

    /** @var Lib\MessageForm|null */
    protected $form = null;
    /** @var MapperException|null */
    protected $error = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var Forward */
    protected $forward = null;

    public function __construct()
    {
        Config::load('Short');
        $this->initTModuleTemplate();
        $this->form = new Lib\MessageForm('editMessage');
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->form->composeForm(new Lib\ShortMessage()); // must be without file!!!
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $adapter = new Lib\MessageAdapter($this->inputs, Config::getPath());
                try {
                    $record = $adapter->getRecord();
                } catch (ShortException $ex) { // create file when not exists
                    $adapter->createRecordFile();
                    $record = $adapter->getRecord();
                }
                $ex = new DataExchange($record);
                $ex->import($this->form->getValues());
                $record->date = time();
                if ($record->save(true)) {
                    Notification::addSuccess(Lang::get('short.updated'));
                    $this->isProcessed = true;
                }
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
            $this->forward->forward($this->isProcessed);
            $editTmpl = new Lib\EditTemplate();
            return $out->setContent($this->outModuleTemplate($editTmpl->setData($this->form, Lang::get('short.add_record'))->render()));
        } catch (FormsException $ex) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage()));
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
        return Lang::get('short.page') . ' - ' . Lang::get('short.add_record');
    }
}
