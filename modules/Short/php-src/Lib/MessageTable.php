<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_extras\Forward;
use kalanis\kw_forms\Adapters;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_pager\BasicPager;
use kalanis\kw_paging\Positions;
use kalanis\kw_table\Connector\Form;
use kalanis\kw_table\Connector\Form\KwForm;
use kalanis\kw_table\Connector\PageLink;
use kalanis\kw_table\Helper;
use kalanis\kw_table\Table;
use kalanis\kw_table\Table\Columns;
use kalanis\kw_table\Table\Rules;
use kalanis\kw_table\Table\Sorter;
use KWCMS\modules\Admin\Shared\SimplifiedPager;


/**
 * Class MessageTable
 * @package KWCMS\modules\Short\Lib
 */
class MessageTable
{
    /** @var IVariables|null */
    protected $variables = null;
    /** @var Forward|null */
    protected $forward = null;
    /** @var ExternalLink|null */
    protected $link = null;

    public function __construct(IVariables $inputs, ExternalLink $link)
    {
        $this->variables = $inputs;
        $this->forward = new Forward();
        $this->link = $link;
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
        // full table init
        $table = new Table();
        $inputVariables = new Adapters\InputVarsAdapter($this->variables);
        $inputFiles = new Adapters\InputFilesAdapter($this->variables);
        $form = new \kalanis\kw_forms\Form('messagesForm');
        $table->addHeaderFilter(new KwForm($form));
        $form->setInputs($inputVariables, $inputFiles);

        // sorter links
        $sorter = new Sorter(new Handler(new Sources\Inputs($this->variables)));
        $table->addSorter($sorter);

        // pager
        $pager = new BasicPager();
        $pageLink = new PageLink(new Handler(new Sources\Inputs($this->variables)), $pager);
        $pager->setActualPage($pageLink->getPageNumber());
        $table->addPager(new SimplifiedPager(new Positions($pager), $pageLink));

        // now normal code - columns
        $table->setDefaultSorting('id', \kalanis\kw_mapper\Interfaces\IQueryBuilder::ORDER_DESC);

        $table->addHeaderFilter($table->getHeaderFilter()->getConnector()); // use that form in header which won't be used here
        $table->setDefaultHeaderFilterFieldAttributes(['style' => 'width:90%']);

        $columnUserId = new Columns\Func('id', [$this, 'idLink']);
        $columnUserId->style('width:40px', new Rules\Always());
        $table->addSortedColumn(Lang::get('short.id'), $columnUserId );

        $columnAdded = new Columns\Date('date', 'Y-m-d H:i:s');
        $columnAdded->style('width:150px', new Rules\Always());
        $table->addSortedColumn(Lang::get('short.date'), $columnAdded);

        $table->addSortedColumn(Lang::get('short.title'), new Columns\Bold('title'), new Form\KwField\TextContains());
        $table->addSortedColumn(Lang::get('short.message'), new Columns\Basic('content'), new Form\KwField\TextContains());

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'id');
        $columnActions->addColumn(new Columns\Func('id', [$this, 'editLink']));
        $columnActions->addColumn(new Columns\Func('id', [$this, 'deleteLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $table->addColumn(Lang::get('short.actions'), $columnActions);
//        $table->addColumn('Actions', $columnActions, null, new Form\KwField\Options(static::getStatuses(), [
//            'id' => 'multiselectChange',
//            'data-toggle' => 'modal-ajax-wide-table',
//        ]));
//        $columnCheckbox = new Columns\Multi('&nbsp;&nbsp;', 'checkboxes');
//        $columnCheckbox->addColumn(new Columns\MultiSelectCheckbox('id'));
//        $table->addColumn('', $columnCheckbox, null, new Form\KwField\MultiSelect( '0', ['id' => 'multiselectAll']) );

        $pager->setLimit(10);
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
        $this->forward->setLink($this->link->linkVariant('short/edit/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('short/dashboard'));
        return sprintf('<a href="%s" class="button">%s</a>',
            $this->forward->getLink(),
            strval($id)
        );
    }

    public function editLink($id)
    {
        $this->forward->setLink($this->link->linkVariant('short/edit/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('short/dashboard'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x25B6; </a>',
            $this->forward->getLink(),
            Lang::get('short.update_texts')
        );
    }

    public function deleteLink($id)
    {
        $this->forward->setLink($this->link->linkVariant('short/delete/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('short/dashboard'));
        return sprintf('<a href="%s" title="%s" class="button button-delete"> &#x1F7AE; </a>',
            $this->forward->getLink(),
            Lang::get('short.remove_record')
        );
    }
}
