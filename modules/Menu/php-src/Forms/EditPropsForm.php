<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_menu\Menu\Menu;


/**
 * Class EditPropsForm
 * @package KWCMS\modules\Menu\Lib
 * Edit properties of key in menu
 * @property Controls\Text current
 * @property Controls\Text menuName
 * @property Controls\Text menuDesc
 * @property Controls\Text menuCount
 * @property Controls\Submit saveFile
 * @property Controls\Reset resetFile
 */
class EditPropsForm extends Form
{
    public function composeForm(Menu $menu): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('current', Lang::get('menu.current_dir'), $menu->getFile(), ['class' => 'file', 'disabled' => 'disabled']);
        $this->addText('menuName', Lang::get('menu.name_in_menu'), $menu->getName(), ['class' => 'name']);
        $this->addText('menuDesc', Lang::get('menu.desc_in_menu'), $menu->getTitle(), ['class' => 'desc']);
        $this->addText('menuCount', Lang::get('menu.showing_lines'), $menu->getDisplayCount(), ['class' => 'pos', 'id' => 'pos_count']);
        $this->addSubmit('saveFile', Lang::get('dashboard.button_set'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
