<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Adapters;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Form;
use kalanis\kw_images\Files;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_pager\BasicPager;
use kalanis\kw_paging\Positions;
use kalanis\kw_table\core\Connector\PageLink;
use kalanis\kw_table\core\Table;
use kalanis\kw_table\core\Table\Columns;
use kalanis\kw_table\core\Table\Rules;
use kalanis\kw_table\core\Table\Order;
use kalanis\kw_table\core\TableException;
use kalanis\kw_table\form_kw\Fields;
use kalanis\kw_table\form_kw\KwFilter;
use kalanis\kw_table\output_kw\KwRenderer;
use kalanis\kw_tree\Tree;
use KWCMS\modules\Admin\Shared\SimplifiedPager;


/**
 * Class ListTable
 * @package KWCMS\modules\Images\Lib
 */
class ListTable
{
    /** @var IVariables|null */
    protected $variables = null;
    /** @var ExternalLink|null */
    protected $link = null;
    /** @var string */
    protected $whereDir = '';
    /** @var string */
    protected $libGallery = null;

    public function __construct(IVariables $inputs, ExternalLink $link, Files $libGallery, string $whereDir)
    {
        $this->variables = $inputs;
        $this->link = $link;
        $this->whereDir = $whereDir;
        $this->libGallery = $libGallery;
    }

    /**
     * @param Tree $tree
     * @return Table
     * @throws FormsException
     * @throws ConnectException
     * @throws TableException
     */
    public function getTable(Tree $tree): Table
    {
        // full table init
        $table = new Table();
        $inputVariables = new Adapters\InputVarsAdapter($this->variables);
        $inputFiles = new Adapters\InputFilesAdapter($this->variables);
        $form = new Form('imagesForm');
        $table->addHeaderFilter(new KwFilter($form));
        $form->setInputs($inputVariables, $inputFiles);

        // sorter links
        $sorter = new Order(new Handler(new Sources\Inputs($this->variables)));
        $table->addOrder($sorter);

        // pager
        $pager = new BasicPager();
        $pageLink = new PageLink(new Handler(new Sources\Inputs($this->variables)), $pager);
        $pager->setActualPage($pageLink->getPageNumber());
        $table->addPager(new SimplifiedPager(new Positions($pager), $pageLink));

        // now normal code - columns
        $table->addOrdering('name', IQueryBuilder::ORDER_DESC);
        $table->setDefaultHeaderFilterFieldAttributes(['style' => 'width:90%']);

        $columnThumbLink = new Columns\MultiColumnLink('thumb', [new Columns\Basic('name')], [$this, 'imageLink']);
        $columnThumbLink->style('width:140px', new Rules\Always());
        $table->addColumn(Lang::get('images.thumb'), $columnThumbLink );

        $table->addOrderedColumn(Lang::get('images.name'), new Columns\Bold('name'), new Fields\TextContains());
        $table->addOrderedColumn(Lang::get('images.size'), new Columns\Basic('size'), new Fields\Multiple([
            new Fields\MultipleValue(new Fields\NumFrom(), Lang::get('images.filter.from')),
            new Fields\MultipleValue(new Fields\NumToWith(), Lang::get('images.filter.to'))
        ]));

        $table->addOrderedColumn(Lang::get('images.desc'), new Columns\Basic('desc'), new Fields\TextContains());

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'name');
        $columnActions->addColumn(new Columns\Func('name', [$this, 'editLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $table->addColumn(Lang::get('images.actions'), $columnActions);

        $pager->setLimit(10);
        $table->addDataSetConnector(new ConnectArray($tree->getTree()->getSubNodes(), $this->whereDir, $this->libGallery));
        $table->setOutput(new KwRenderer($table));
        return $table;
    }

    public function imageLink($data)
    {
        return sprintf('<a href="%s" class="button"><img src="%s" title="%s"></a>',
            $this->link->linkVariant('images/edit/?name=' . $data[1]),
            $this->link->linkVariant($data[0], 'image', true, false),
            strval($data[1])
        );
    }

    public function editLink($name)
    {
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x1F589; </a>',
            $this->link->linkVariant('images/edit/?name=' . $name),
            Lang::get('images.update_item')
        );
    }
}
