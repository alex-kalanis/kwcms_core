<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;
use kalanis\kw_tree\FileNode;
use kalanis\kw_tree_controls\Controls\DirSelect;
use kalanis\kw_tree_controls\Controls\FileCheckboxes;
use kalanis\kw_tree_controls\Controls\FileRadio;


/**
 * Class FileForm
 * @package KWCMS\modules\Files\Lib
 * Process files in many ways
 */
class FileForm extends AForm
{
    public function composeUploadFile(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addFile('uploadedFile', Lang::get('files.file.select'))
            ->addRule(IRules::FILE_RECEIVED, Lang::get('files.must_be_sent'));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeReadFile(FileNode $sourceTree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $checkboxes = new FileRadio();
        $checkboxes->set('sourceName', '', Lang::get('files.file.select'), $sourceTree);
        $this->addControlDefaultKey($checkboxes);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeCopyFile(FileNode $sourceTree, FileNode $targetTree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $checkboxes = new FileCheckboxes();
        $checkboxes->set('sourceName[]', '', Lang::get('files.file.selectMany'), $sourceTree);
        $this->addControlDefaultKey($checkboxes);

        $radios = new DirSelect();
        $radios->set('targetPath', '', Lang::get('files.dir.selectTo'), $targetTree);
        $this->addControlDefaultKey($radios);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeMoveFile(FileNode $sourceTree, FileNode $targetTree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $checkboxes = new FileCheckboxes();
        $checkboxes->set('sourceName[]', '', Lang::get('files.file.selectMany'), $sourceTree);
        $this->addControlDefaultKey($checkboxes);

        $radios = new DirSelect();
        $radios->set('targetPath', '', Lang::get('files.dir.selectTo'), $targetTree);
        $this->addControlDefaultKey($radios);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeRenameFile(FileNode $tree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $radios = new FileRadio();
        $radios->set('sourceName', '', Lang::get('files.file.select'), $tree);
        $this->addControlDefaultKey($radios);

        $this->addText('targetPath', Lang::get('files.file.newName'));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeDeleteFile(FileNode $tree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $checkboxes = new FileCheckboxes();
        $checkboxes->set('sourceName[]', '', Lang::get('files.file.selectMany'), $tree);
        $this->addControlDefaultKey($checkboxes);

        $radios = new Controls\RadioSet();
        $radios->set('targetPath', 'no', Lang::get('files.check.really'), [
            'yes' => Lang::get('files.check.yes'),
            'no' => Lang::get('files.check.no'),
        ]);
        $this->addControlDefaultKey($radios);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
