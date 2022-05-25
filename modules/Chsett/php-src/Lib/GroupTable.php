<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_auth\Sources\Files;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Adapters;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_modules\Linking\ExternalLink;
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


/**
 * Class GroupTable
 * @package KWCMS\modules\Chsett\Lib
 */
class GroupTable
{
    /** @var IVariables|null */
    protected $variables = null;
    /** @var ExternalLink|null */
    protected $link = null;
    /** @var Files|null */
    protected $libAuth = null;
    /** @var IUser|null */
    protected $currentUser = null;
    /** @var Forward|null */
    protected $forward = null;

    public function __construct(IVariables $inputs, ExternalLink $link, Files $libAuth, IUser $currentUser)
    {
        $this->variables = $inputs;
        $this->link = $link;
        $this->libAuth = $libAuth;
        $this->currentUser = $currentUser;
        $this->forward = new Forward();
    }

    /**
     * @return Table
     * @throws AuthException
     * @throws ConnectException
     * @throws FormsException
     * @throws LockException
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
        $table->addDataSetConnector(new ConnectGroupArray($this->libAuth->readGroup()));
        $table->setOutput(new KwRenderer($table));
        return $table;
    }

    public function idLink($id)
    {
        $this->forward->setLink($this->link->linkVariant('chsett/group/edit/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('chsett/groups'));
        return sprintf('<a href="%s" class="button">%s</a>',
            $this->forward->getLink(),
            strval($id)
        );
    }

    public function editLink($id)
    {
        $this->forward->setLink($this->link->linkVariant('chsett/group/edit/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('chsett/groups'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x1F589; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.edit_group')
        );
    }

    public function deleteLink($id)
    {
        $this->forward->setLink($this->link->linkVariant('chsett/group/delete/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('chsett/groups'));
        return sprintf('<a href="%s" title="%s" class="button button-delete"> &#x1F7AE; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.remove_group')
        );
    }
}
