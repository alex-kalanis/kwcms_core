<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree_controls\Controls\DirCheckboxes;
use kalanis\kw_tree_controls\Controls\DirSelect;
use kalanis\kw_tree_controls\Controls\DirRadio;


/**
 * Class DirForm
 * @package KWCMS\modules\Files\Lib
 * Process dirs in many ways
 */
class DirForm extends AForm
{
    public function composeCreateDir(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('targetPath', Lang::get('files.dir.newName'));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeCopyDir(FileNode $sourceTree, FileNode $targetTree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $checkboxes = new DirCheckboxes();
        $checkboxes->set('sourceName[]', '', Lang::get('files.dir.selectMany'), $sourceTree);
        $this->addControlDefaultKey($checkboxes);

        $radios = new DirSelect();
        $radios->set('targetPath', '', Lang::get('files.dir.selectTo'), $targetTree);
        $this->addControlDefaultKey($radios);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeMoveDir(FileNode $sourceTree, FileNode $targetTree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $checkboxes = new DirCheckboxes();
        $checkboxes->set('sourceName[]', '', Lang::get('files.dir.selectMany'), $sourceTree);
        $this->addControlDefaultKey($checkboxes);

        $radios = new DirSelect();
        $radios->set('targetPath', '', Lang::get('files.dir.selectTo'), $targetTree);
        $this->addControlDefaultKey($radios);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeRenameDir(FileNode $tree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $radios = new DirRadio();
        $radios->set('sourceName', '', Lang::get('files.dir.select'), $tree);
        $this->addControlDefaultKey($radios);

        $this->addText('targetPath', Lang::get('files.dir.newName'));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function composeDeleteDir(FileNode $tree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $checkboxes = new DirCheckboxes();
        $checkboxes->set('sourceName[]', '', Lang::get('files.dir.selectMany'), $tree);
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
