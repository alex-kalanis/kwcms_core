<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces\IProcessGroups;
use kalanis\kw_accounts\Interfaces\IUser;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_forms\Adapters;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
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
use KWCMS\modules\Admin\Shared\SimplifiedPager;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class GroupTable
 * @package KWCMS\modules\Chsett\Lib
 */
class GroupTable
{
    /** @var IFiltered|null */
    protected $variables = null;
    /** @var ExternalLink|null */
    protected $link = null;
    /** @var IProcessGroups|null */
    protected $libGroups = null;
    /** @var IUser|null */
    protected $currentUser = null;
    /** @var Forward|null */
    protected $forward = null;

    public function __construct(IFiltered $inputs, ExternalLink $link, IProcessGroups $groups, IUser $currentUser)
    {
        $this->variables = $inputs;
        $this->link = $link;
        $this->libGroups = $groups;
        $this->currentUser = $currentUser;
        $this->forward = new Forward();
    }

    /**
     * @return Table
     * @throws AccountsException
     * @throws FormsException
     * @throws LangException
     * @throws TableException
     */
    public function getTable()
    {
        // full table init
        $table = new Table();
        $inputVariables = new Adapters\InputVarsAdapter($this->variables);
        $form = new Form('filterForm');
        $table->addHeaderFilter(new KwFilter($form));
        $form->setInputs($inputVariables);

        // order links
        $table->addOrder(new Order(new Handler(new Sources\Inputs($this->variables))));

        // pager
        $pager = new BasicPager();
        $pageLink = new PageLink(new Handler(new Sources\Inputs($this->variables)), $pager);
        $pager->setActualPage($pageLink->getPageNumber());
        $table->addPager(new SimplifiedPager(new Positions($pager), $pageLink));

        // now normal code - columns
        $table->addOrdering('id', IQueryBuilder::ORDER_DESC);

        $table->setDefaultHeaderFilterFieldAttributes(['style' => 'width:90%']);

        $columnUserId = new Columns\Func('id', [$this, 'idLink']);
        $columnUserId->style('width:40px', new Rules\Always());
        $table->addOrderedColumn(Lang::get('chsett.group_id'), $columnUserId );

        $table->addOrderedColumn(Lang::get('chsett.group_name'), new Columns\Basic('name'), new Fields\TextContains());
        $table->addOrderedColumn(Lang::get('chsett.group_desc'), new Columns\Basic('desc'), new Fields\TextContains());

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'id');
        $columnActions->addColumn(new Columns\Func('id', [$this, 'editLink']));
        $columnActions->addColumn(new Columns\Func('id', [$this, 'deleteLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $table->addColumn(Lang::get('chsett.table.actions'), $columnActions);

        $pager->setLimit(10);
        $table->addDataSetConnector(new ConnectGroupArray($this->libGroups->readGroup()));
        $table->setOutput(new KwRenderer($table));
        return $table;
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function idLink($id): string
    {
        $this->forward->setLink($this->link->linkVariant('chsett/group/edit/?id=' . strval($id)));
        $this->forward->setForward($this->link->linkVariant('chsett/groups'));
        return sprintf('<a href="%s" class="button">%s</a>',
            $this->forward->getLink(),
            strval($id)
        );
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function editLink($id): string
    {
        $this->forward->setLink($this->link->linkVariant('chsett/group/edit/?id=' . strval($id)));
        $this->forward->setForward($this->link->linkVariant('chsett/groups'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x1F589; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.edit_group')
        );
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function deleteLink($id): string
    {
        $this->forward->setLink($this->link->linkVariant('chsett/group/delete/?id=' . strval($id)));
        $this->forward->setForward($this->link->linkVariant('chsett/groups'));
        return sprintf('<a href="%s" title="%s" class="button button-delete"> &#x1F7AE; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.remove_group')
        );
    }
}
