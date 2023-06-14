<?php

namespace KWCMS\modules\Pedigree\AdminControllers;


use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\Adapters\DataExchange;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Stored;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_scripts\Scripts;
use KWCMS\modules\Pedigree\Lib;


/**
 * Class Edit
 * @package KWCMS\modules\Pedigree\AdminControllers
 * Site's Pedigree - edit form
 */
class Edit extends APedigree
{
    /** @var Lib\MessageForm|null */
    protected $form = null;
    /** @var GetEntries */
    protected $entry = null;
    /** @var ExternalLink */
    protected $extLink = null;

    public function __construct()
    {
        parent::__construct();
        $this->form = new Lib\MessageForm('editPedigree');
        $this->extLink = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
    }

    public function run(): void
    {
        try {
            $this->entry = new GetEntries($this->getRecord());
            $record = $this->entry->getRecord();
            $this->entry->getStorage()->setRecord($record);
            $record->offsetSet($this->entry->getStorage()->getKeyKey(), strval($this->getFromParam('key')));
            $record->load();
            $this->form->composeForm($this->entry, $this->extLink->linkVariant('pedigree/lookup'));
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $processEntry = false;
                $ex = new DataExchange($record);
                $ex->addExclude('fatherId');
                $ex->addExclude('motherId');
                if ((bool)$ex->import($this->form->getValues())) {
                    if ($record->save()) {
                        $record->load();
                        $this->entry->getStorage()->setRecord($record);
                        $processEntry = true;
                    }
                }
                $processFamily = $this->entry->getStorage()->saveFamily(
                    $this->form->getControl('fatherId')->getValue(),
                    $this->form->getControl('motherId')->getValue()
                );
                $this->isProcessed = $processEntry || (true === $processFamily);
            }
        } catch (MapperException | FormsException | PedigreeException $ex) {
            $this->error = $ex;
        }
    }

    protected function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            }
            if ($this->isProcessed) {
                Notification::addSuccess(Lang::get('pedigree.updated'));
            }
            $this->forward->forward($this->isProcessed);
            Scripts::want('Pedigree', 'names.js');
            $editTmpl = new Lib\EditTemplate();
            return $out->setContent($this->outModuleTemplate($editTmpl->setData($this->form, $this->entry, Lang::get('pedigree.update'))->render()));
        } catch (FormsException $ex) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        }
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    protected function outJson(): Output\AOutput
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
        return Lang::get('pedigree.page') . ' - ' . Lang::get('pedigree.update');
    }
}
