<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;
use kalanis\kw_tree\Controls\DirSelect;
use kalanis\kw_tree\Controls\FileRadio;
use kalanis\kw_tree\FileNode;


/**
 * Class FileForm
 * @package KWCMS\modules\Files\Lib
 * Process files in many ways
 * @property Controls\File uploadedFile
 * @property FileRadio fileName
 * @property DirSelect|Controls\Text targetPath
 * @property Controls\Submit saveFile
 * @property Controls\Reset resetFile
 */
class FileForm extends Form
{
    public function composeUploadFile(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addFile('uploadedFile')
            ->addRule(IRules::FILE_RECEIVED, Lang::get('files.must_be_sent'));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeCopyFile(FileNode $sourceTree, FileNode $targetTree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $radios = new FileRadio();
        $radios->set('fileName', null, Lang::get('texts.set_file'), $sourceTree);
        $this->addControl($radios);

        $radios = new DirSelect();
        $radios->set('targetPath', null, Lang::get('texts.set_file'), $targetTree);
        $this->addControl($radios);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeMoveFile(FileNode $sourceTree, FileNode $targetTree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $radios = new FileRadio();
        $radios->set('fileName', null, Lang::get('texts.set_file'), $sourceTree);
        $this->addControl($radios);

        $radios = new DirSelect();
        $radios->set('targetPath', null, Lang::get('texts.set_file'), $targetTree);
        $this->addControl($radios);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeRenameFile(FileNode $tree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $radios = new FileRadio();
        $radios->set('fileName', null, Lang::get('texts.set_file'), $tree);
        $this->addControl($radios);

        $this->addText('targetPath', null, Lang::get(''));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeDeleteFile(FileNode $tree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $radios = new FileRadio();
        $radios->set('fileName', null, Lang::get('texts.set_file'), $tree);
        $this->addControl($radios);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
