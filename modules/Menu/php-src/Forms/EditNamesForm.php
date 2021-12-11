<?php

namespace KWCMS\modules\Menu\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_menu\Menu\Item;


/**
 * Class EditNamesForm
 * @package KWCMS\modules\Menu\Forms
 * Edit Names of items in menu
 * @property Controls\Text current
 * @property Controls\Text menuName
 * @property Controls\Text menuDesc
 * @property Controls\Text menuGoSub
 * @property Controls\Submit saveFile
 * @property Controls\Reset resetFile
 */
class EditNamesForm extends Form
{
    public function composeForm(Item $item): self
    {
        $this->setMethod(IEntry::SOURCE_POST);

        $this->addText('current', Lang::get('menu.entry_name'), $item->getFile(), ['class' => 'file', 'disabled' => 'disabled']);
        $this->addText('menuName', Lang::get('menu.name_in_menu'), $item->getName(), ['class' => 'name']);
        $this->addText('menuDesc', Lang::get('menu.desc_in_menu'), $item->getTitle(), ['class' => 'desc']);
        $this->addRadios('menuGoSub', Lang::get('menu.submenu'), intval($item->canGoSub()), [
            0 => Lang::get('dashboard.button_no'),
            1 => Lang::get('dashboard.button_yes'),
        ]);

        $this->addSubmit('saveFile', Lang::get('dashboard.button_set'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
