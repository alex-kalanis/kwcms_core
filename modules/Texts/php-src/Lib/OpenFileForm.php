<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_tree\Controls\FileRadio;
use kalanis\kw_tree\FileNode;


/**
 * Class OpenFileForm
 * @package KWCMS\modules\Texts\Lib
 * Open file
 * @property FileRadio fileName
 * @property Controls\Submit openFile
 * @property Controls\Reset resetFile
 */
class OpenFileForm extends Form
{
    public function composeForm(string $defaultWhere, ?FileNode $tree, ExternalLink $links): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->setAttribute('target', $links->linkVariant('texts/edit'));

        $radios = new FileRadio();
        $radios->set('fileName', $defaultWhere, Lang::get('texts.set_file'), $tree);
        $this->addControl($radios);

        $this->addSubmit('openFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
