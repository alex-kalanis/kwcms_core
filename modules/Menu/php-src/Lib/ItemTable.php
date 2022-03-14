<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Form;
use kalanis\kw_langs\Lang;
use kalanis\kw_menu\MetaProcessor;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_table\core\Table;
use kalanis\kw_table\core\Table\Columns;
use kalanis\kw_table\core\Table\Rules;
use kalanis\kw_table\core\TableException;
use kalanis\kw_table\form_kw\KwFilter;
use kalanis\kw_table\output_json\JsonRenderer;
use kalanis\kw_table\output_kw\KwRenderer;


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
     * @param MetaProcessor $data
     * @return string
     * @throws ConnectException
     * @throws TableException
     */
    public function prepareHtml(MetaProcessor $data)
    {
        // cut table init - no search
        $table = new Table();

        // just for styles...
        $form = new Form('messagesForm');
        $table->addHeaderFilter(new KwFilter($form));

        $table->addColumn(Lang::get('menu.entry_name'), new Columns\Func('id', [$this, 'idLink']));
        $table->addColumn(Lang::get('menu.name_in_menu'), new Columns\Basic('name'));
        $table->addColumn(Lang::get('menu.desc_in_menu'), new Columns\Basic('desc'));
        $table->addColumn(Lang::get('menu.submenu'), new Columns\Map('sub', [
            0 => Lang::get('dashboard.button_no'),
            1 => Lang::get('dashboard.button_yes'),
        ]));

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'file');
        $columnActions->addColumn(new Columns\Func('id', [$this, 'editLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $table->addColumn(Lang::get('menu.actions'), $columnActions);

        $table->addDataSetConnector(new ItemConnector($data->getWorking()));
        $output = new KwRenderer($table);
        $table->translateData();
        return $output->render();
    }

    /**
     * @param MetaProcessor $data
     * @return mixed
     * @throws ConnectException
     * @throws TableException
     */
    public function prepareJson(MetaProcessor $data)
    {
        $table = new Table();

        $table->addColumn(Lang::get('menu.entry_name'), new Columns\Basic('id'));
        $table->addColumn(Lang::get('menu.name_in_menu'), new Columns\Basic('name'));
        $table->addColumn(Lang::get('menu.desc_in_menu'), new Columns\Basic('desc'));
        $table->addColumn(Lang::get('menu.position'), new Columns\Basic('pos'));
        $table->addColumn(Lang::get('menu.submenu'), new Columns\Basic('sub'));

        $table->addDataSetConnector(new ItemConnector($data->getWorking()));
        $table->translateData();
        $output = new JsonRenderer($table);
        return $output->renderData();
    }

    public function idLink($name)
    {
        $this->forward->setLink($this->link->linkVariant('menu/edit/?id=' . $name));
        $this->forward->setForward($this->link->linkVariant('menu/names'));
        return sprintf('<a href="%s" class="button">%s</a>',
            $this->forward->getLink(),
            strval($name)
        );
    }

    public function editLink($name)
    {
        $this->forward->setLink($this->link->linkVariant('menu/edit/?id=' . $name));
        $this->forward->setForward($this->link->linkVariant('menu/names'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x25B6; </a>',
            $this->forward->getLink(),
            Lang::get('menu.update_texts')
        );
    }
}
