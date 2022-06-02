<?php

namespace KWCMS\modules\Pedigree;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\Adapters\DataExchange;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Stored;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage;
use kalanis\kw_scripts\Scripts;


/**
 * Class Add
 * @package KWCMS\modules\Pedigree
 * Site's Pedigree - add form
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
    /** @var GetEntries */
    protected $entry = null;
    /** @var ExternalLink */
    protected $extLink = null;

    public function __construct()
    {
        Config::load('Pedigree');
        $this->initTModuleTemplate();
        $this->form = new Lib\MessageForm('editPedigree');
        $this->extLink = new ExternalLink(Stored::getPath());
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
            $this->entry = new GetEntries($this->getRecord());
            $this->entry->getStorage()->setRecord($this->entry->getRecord());
            $this->form->composeForm($this->entry, $this->extLink->linkVariant('pedigree/lookup'));
            $this->form->addIdentifier();
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $ex = new DataExchange($this->entry->getRecord());
                $ex->addExclude('fatherId');
                $ex->addExclude('motherId');
                $ex->import($this->form->getValues());
                if ($this->entry->getRecord()->save(true)) {
                    $this->entry->getRecord()->load();
                    $this->entry->getStorage()->setRecord($this->entry->getRecord());
                    $this->isProcessed = $this->entry->getStorage()->saveFamily(
                        $this->form->getControl('fatherId')->getValue(),
                        $this->form->getControl('motherId')->getValue()
                    );
                };
            }
        } catch (MapperException | FormsException | PedigreeException | \PDOException $ex) {
            $this->error = $ex;
        }
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

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            }
            if ($this->isProcessed) {
                Notification::addSuccess(Lang::get('pedigree.added'));
            }
            $this->forward->forward($this->isProcessed);
            Scripts::want('Pedigree', 'names.js');
            $editTmpl = new Lib\EditTemplate();
            return $out->setContent($this->outModuleTemplate($editTmpl->setData($this->form, $this->entry, Lang::get('pedigree.add_record'))->render()));
        } catch (FormsException $ex) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
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
        return Lang::get('pedigree.page') . ' - ' . Lang::get('pedigree.add_record');
    }
}
