<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Form;
use kalanis\kw_langs\Lang;
use kalanis\kw_menu\DataProcessor;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_table\core\Table;
use kalanis\kw_table\core\Table\Columns;
use kalanis\kw_table\core\Table\Rules;
use kalanis\kw_table\core\TableException;
use kalanis\kw_table\form_kw\KwFilter;


/**
 * Class ItemTable
 * @package KWCMS\modules\Menu\Lib
 */
class ItemTable
{
    /** @var Forward|null */
    protected $forward = null;
    /** @var ExternalLink|null */
    protected $link = null;

    public function __construct(ExternalLink $link)
    {
        $this->forward = new Forward();
        $this->link = $link;
    }

    /**
     * @param DataProcessor $data
     * @return string
     * @throws ConnectException
     * @throws TableException
     */
    public function prepareHtml(DataProcessor $data)
    {
        // cut table init - no search
        $table = new Table();

        // just for styles...
        $form = new Form('messagesForm');
        $table->addHeaderFilter(new KwFilter($form));

        $table->addColumn(Lang::get('menu.entry_name'), new Columns\Func('file', [$this, 'idLink']));
        $table->addColumn(Lang::get('menu.name_in_menu'), new Columns\Basic('name'));
        $table->addColumn(Lang::get('menu.desc_in_menu'), new Columns\Basic('desc'));
        $table->addColumn(Lang::get('menu.submenu'), new Columns\Map('sub', [
            0 => Lang::get('dashboard.button_no'),
            1 => Lang::get('dashboard.button_yes'),
        ]));

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'file');
        $columnActions->addColumn(new Columns\Func('file', [$this, 'editLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $table->addColumn(Lang::get('menu.actions'), $columnActions);

        $table->addDataSetConnector(new ItemConnector($data->getWorking()));
        return $table->render();
    }

    /**
     * @param DataProcessor $data
     * @return mixed
     * @throws ConnectException
     * @throws TableException
     */
    public function prepareJson(DataProcessor $data)
    {
        $table = new Table();

        $table->addColumn(Lang::get('menu.entry_name'), new Columns\Basic('file'));
        $table->addColumn(Lang::get('menu.name_in_menu'), new Columns\Basic('name'));
        $table->addColumn(Lang::get('menu.desc_in_menu'), new Columns\Basic('desc'));
        $table->addColumn(Lang::get('menu.position'), new Columns\Basic('pos'));
        $table->addColumn(Lang::get('menu.submenu'), new Columns\Basic('sub'));

        $table->addDataSetConnector(new ItemConnector($data->getWorking()));
        $table->translateData();
        return $table->getOutput()->renderData();
    }

    public function idLink($name)
    {
        $this->forward->setLink($this->link->linkVariant('menu/edit/?filename=' . $name));
        $this->forward->setForward($this->link->linkVariant('menu/names'));
        return sprintf('<a href="%s" class="button">%s</a>',
            $this->forward->getLink(),
            strval($name)
        );
    }

    public function editLink($name)
    {
        $this->forward->setLink($this->link->linkVariant('menu/edit/?filename=' . $name));
        $this->forward->setForward($this->link->linkVariant('menu/names'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x25B6; </a>',
            $this->forward->getLink(),
            Lang::get('menu.update_texts')
        );
    }
}
