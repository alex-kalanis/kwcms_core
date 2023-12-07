<?php

namespace KWCMS\modules\Pedigree\AdminControllers;


use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;


/**
 * Class Delete
 * @package KWCMS\modules\Pedigree\AdminControllers
 * Site's Pedigree - delete record
 */
class Delete extends APedigree
{
    public function run(): void
    {
        try {
            $entry = new GetEntries($this->getConnectRecord());
            $record = $entry->getRecord();
            $record->offsetSet($entry->getStorage()->getIdKey(), strval($this->getFromParam('key')));
            $this->isProcessed = $record->delete();
        } catch (MapperException | PedigreeException $ex) {
            $this->error = $ex;
        }
    }

    protected function outHtml(): Output\AOutput
    {
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        if ($this->isProcessed) {
            Notification::addSuccess(Lang::get('pedigree.removed'));
        }
        $this->forward->forward();
        $this->forward->setForward($this->links->linkVariant('pedigree/dashboard'));
        $this->forward->forward();
        return new Output\Raw();
    }

    protected function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            return $out->setContent(['Success']);
        }
    }

    public function getTitle(): string
    {
        return Lang::get('pedigree.page') . ' - ' . Lang::get('pedigree.remove');
    }
}
