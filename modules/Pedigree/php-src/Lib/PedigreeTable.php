<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\search\Connector;
use kalanis\kw_forms\Adapters;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pager\BasicPager;
use kalanis\kw_paging\Positions;
use kalanis\kw_paths\PathsException;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_table\core\Connector\PageLink;
use kalanis\kw_table\core\Table;
use kalanis\kw_table\core\Table\Columns;
use kalanis\kw_table\core\Table\Rules;
use kalanis\kw_table\core\Table\Order;
use kalanis\kw_table\core\TableException;
use kalanis\kw_table\form_kw\Fields;
use kalanis\kw_table\form_kw\KwFilter;
use kalanis\kw_table\kw\Helper;
use kalanis\kw_table\output_kw\KwRenderer;
use KWCMS\modules\Admin\Shared\SimplifiedPager;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class PedigreeTable
 * @package KWCMS\modules\Pedigree\Lib
 */
class PedigreeTable
{
    protected IFiltered $variables;
    protected Forward $forward;
    protected ExternalLink $link;
    protected GetEntries $entries;
    protected Table $table;

    public function __construct(IFiltered $inputs, ExternalLink $link, GetEntries $entries)
    {
        $this->variables = $inputs;
        $this->forward = new Forward();
        $this->link = $link;
        $this->entries = $entries;
        $this->table = new Table();
    }

    /**
     * @return string
     * @throws ConnectException
     * @throws FormsException
     * @throws HandlerException
     * @throws LangException
     * @throws MapperException
     * @throws TableException
     */
    public function prepareHtml()
    {
        // full table init
        $inputVariables = new Adapters\InputVarsAdapter($this->variables);
        $inputFiles = new Adapters\InputFilesAdapter($this->variables);
        $form = new Form('filterForm');
        $this->table->addHeaderFilter(new KwFilter($form));
        $form->setInputs($inputVariables, $inputFiles);

        // order links
        $this->table->addOrder(new Order(new Handler(new Sources\Inputs($this->variables))));

        // pager
        $pager = new BasicPager();
        $pageLink = new PageLink(new Handler(new Sources\Inputs($this->variables)), $pager);
        $pager->setActualPage($pageLink->getPageNumber());
        $this->table->addPager(new SimplifiedPager(new Positions($pager), $pageLink));

        $storage = $this->entries->getStorage();
        // now normal code - columns
        $this->table->addOrdering($storage->getIdKey(), IQueryBuilder::ORDER_DESC);

        $this->table->setDefaultHeaderFilterFieldAttributes(['style' => 'width:90%']);

        $columnRecordId = new Columns\Func($storage->getIdKey(), [$this, 'idLink']);
        $columnRecordId->style('width:40px', new Rules\Always());
        $this->table->addOrderedColumn(Lang::get('pedigree.text.id'), $columnRecordId );

        $this->table->addOrderedColumn(Lang::get('pedigree.text.name'), new Columns\Bold($storage->getNameKey()), new Fields\TextContains());
        $this->table->addOrderedColumn(Lang::get('pedigree.text.family'), new Columns\Basic($storage->getFamilyKey()), new Fields\TextContains());

        $columnAdded = new Columns\Basic($storage->getBirthKey());
        $columnAdded->style('width:150px', new Rules\Always());
        $this->table->addOrderedColumn(Lang::get('pedigree.text.birth_date'), $columnAdded);

        $this->table->addColumn(Lang::get('pedigree.text.successes'), new Columns\Bold($storage->getSuccessesKey()), new Fields\Multiple([
            new Fields\MultipleValue(new Fields\TextContains()),
            new Fields\MultipleValue(new Fields\TextContains()),
        ]));

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'id');
        $columnActions->addColumn(new Columns\Func('id', [$this, 'showLink']));
        $columnActions->addColumn(new Columns\Func('id', [$this, 'editLink']));
        $columnActions->addColumn(new Columns\Func('id', [$this, 'deleteLink']));
        $columnActions->style('width:200px', new Rules\Always());

        $this->table->addColumn(Lang::get('pedigree.actions'), $columnActions);

        $pager->setLimit(10);
        $this->table->addDataSetConnector(new Connector(new Search(clone $this->entries->getRecord())));
        $this->table->setOutput(new KwRenderer($this->table));
        return $this->table->render();
    }

    /**
     * @return mixed
     * @throws ConnectException
     * @throws MapperException
     * @throws FormsException
     * @throws HandlerException
     * @throws TableException
     */
    public function prepareJson()
    {
        $helper = new Helper();
        $helper->fillKwJson($this->variables);
        $table = $helper->getTable();
        $storage = $this->entries->getStorage();
        $table->addColumn(Lang::get('pedigree.text.id'), new Columns\Basic($storage->getIdKey()));
        $table->addColumn(Lang::get('pedigree.text.short'), new Columns\Basic($storage->getShortKey()));
        $table->addColumn(Lang::get('pedigree.text.name'), new Columns\Basic($storage->getNameKey()));
        $table->addColumn(Lang::get('pedigree.text.family'), new Columns\Basic($storage->getFamilyKey()));
        $table->addColumn(Lang::get('pedigree.text.birth_date'), new Columns\Basic($storage->getBirthKey()));
        $table->addColumn(Lang::get('pedigree.text.successes'), new Columns\Basic($storage->getSuccessesKey()));

        $table->getPager()->getPager()->setLimit(5);
        $table->addOrdering('id', IQueryBuilder::ORDER_DESC);
        $table->addDataSetConnector(new Connector(new Search(clone $this->entries->getRecord())));
        $table->translateData();
        return $table->getOutput()->renderData();
    }

    /**
     * @param string|int $id
     * @throws HandlerException
     * @throws PathsException
     * @throws TableException
     * @return string
     */
    public function idLink($id): string
    {
        $key = $this->table->getDataSetConnector()->getByKey($id)->getValue($this->entries->getStorage()->getIdKey());
        $this->forward->setLink($this->link->linkVariant('pedigree/edit/?key=' . $key));
        $this->forward->setForward($this->link->linkVariant('pedigree/dashboard'));
        return sprintf('<a href="%s" class="button">%s</a>',
            $this->forward->getLink(),
            strval($id)
        );
    }

    /**
     * @param string|int $id
     * @throws HandlerException
     * @throws PathsException
     * @throws TableException
     * @return string
     */
    public function showLink($id): string
    {
        $key = $this->table->getDataSetConnector()->getByKey($id)->getValue($this->entries->getStorage()->getShortKey());
        return sprintf('<a href="%s" title="%s" class="button button-preview"> &#x1F50D; </a>',
            $this->link->linkVariant('pedigree/' . $key, 'pedigree'),
            Lang::get('pedigree.show')
        );
    }

    /**
     * @param string|int $id
     * @throws HandlerException
     * @throws PathsException
     * @throws TableException
     * @return string
     */
    public function editLink($id): string
    {
        $key = $this->table->getDataSetConnector()->getByKey($id)->getValue($this->entries->getStorage()->getIdKey());
        $this->forward->setLink($this->link->linkVariant('pedigree/edit/?key=' . $key));
        $this->forward->setForward($this->link->linkVariant('pedigree/dashboard'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x1F589; </a>',
            $this->forward->getLink(),
            Lang::get('pedigree.update')
        );
    }

    /**
     * @param string|int $id
     * @throws HandlerException
     * @throws PathsException
     * @throws TableException
     * @return string
     */
    public function deleteLink($id): string
    {
        $key = $this->table->getDataSetConnector()->getByKey($id)->getValue($this->entries->getStorage()->getIdKey());
        $this->forward->setLink($this->link->linkVariant('pedigree/delete/?key=' . $key));
        $this->forward->setForward($this->link->linkVariant('pedigree/dashboard'));
        return sprintf('<a href="%s" title="%s" class="button button-delete"> &#x1F7AE; </a>',
            $this->forward->getLink(),
            Lang::get('pedigree.remove')
        );
    }
}
