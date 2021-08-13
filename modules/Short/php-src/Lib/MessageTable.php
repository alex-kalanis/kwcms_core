<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_table\Connector\Form;
use kalanis\kw_table\Helper;
use kalanis\kw_table\Table\Columns;
use kalanis\kw_table\Table\Rules;


/**
 * Class MessageTable
 * @package KWCMS\modules\Short\Lib
 */
class MessageTable
{
    protected $variables = null;

    public function __construct(IVariables $inputs)
    {
        $this->variables = $inputs;
    }

    /**
     * @param Search $search
     * @return string
     * @throws MapperException
     * @throws \kalanis\kw_forms\Exceptions\FormsException
     * @throws \kalanis\kw_table\TableException
     */
    public function prepareHtml(Search $search)
    {
        $helper = new Helper();
        $helper->fillKwPage($this->variables, 'messagesForm');
        $table = $helper->getTable();
        $table->setDefaultSorting('id', \kalanis\kw_mapper\Interfaces\IQueryBuilder::ORDER_DESC);

        $table->addHeaderFilter($table->getHeaderFilter()->getConnector()); // use that form in header which won't be used here
        $table->setDefaultHeaderFilterFieldAttributes(['style' => 'width:100%']);

        $columnUserId = new Columns\Func('id', [$this, 'idLink']);
        $columnUserId->style('width:40px', new Rules\Always());
        $table->addSortedColumn(Lang::get('short.id'), $columnUserId );

        $columnAdded = new Columns\Date('date', 'Y-m-d H:i:s');
        $columnAdded->style('width:150px', new Rules\Always());
        $table->addSortedColumn(Lang::get('short.date'), $columnAdded);

        $table->addSortedColumn(Lang::get('short.title'), new Columns\Bold('title'), new Form\KwField\TextContains());
        $table->addSortedColumn(Lang::get('short.message'), new Columns\Basic('content'), new Form\KwField\TextContains());

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'id');
        $columnActions->addColumn(new Columns\Func('id', [$this, 'viewLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $table->addColumn(Lang::get('short.actions'), $columnActions);
//        $table->addColumn('Actions', $columnActions, null, new Form\KwField\Options(static::getStatuses(), [
//            'id' => 'multiselectChange',
//            'data-toggle' => 'modal-ajax-wide-table',
//        ]));
//        $columnCheckbox = new Columns\Multi('&nbsp;&nbsp;', 'checkboxes');
//        $columnCheckbox->addColumn(new Columns\MultiSelectCheckbox('id'));
//        $table->addColumn('', $columnCheckbox, null, new Form\KwField\MultiSelect( '0', ['id' => 'multiselectAll']) );

        $table->getOutputPager()->getPager()->setLimit(10);
        $table->addDataSource(new \kalanis\kw_table\Connector\Sources\Search($search));
        return $table->render();
    }

    /**
     * @param Search $search
     * @return mixed
     * @throws MapperException
     * @throws \kalanis\kw_forms\Exceptions\FormsException
     * @throws \kalanis\kw_table\TableException
     */
    public function prepareJson(Search $search)
    {
        $helper = new Helper();
        $helper->fillKwJson($this->variables);
        $table = $helper->getTable();
        $table->addColumn(Lang::get('short.id'), new Columns\Basic('id'));
        $table->addColumn(Lang::get('short.date'), new Columns\Date('date', 'Y-m-d H:i:s'));
        $table->addColumn(Lang::get('short.title'), new Columns\Basic('title'));
        $table->addColumn(Lang::get('short.message'), new Columns\Basic('content'));

        $table->getOutputPager()->getPager()->setLimit(5);
        $table->setDefaultSorting('id', \kalanis\kw_mapper\Interfaces\IQueryBuilder::ORDER_DESC);
        $table->addDataSource(new \kalanis\kw_table\Connector\Sources\Search($search));
        $table->translateData();
        return $table->getOutput()->renderData();
    }

    public function idLink($id)
    {
        return '<a href="/web/short/edit/?id=' . $id . '">' . $id . '</a>';
    }

    public function viewLink($id)
    {
        return '<a target="_blank" href="/web/short/edit/?id=' . $id . '" title="View"><span class="fa fa-search">&nbsp;</span></a>';
    }
}
