<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces\IGroup;
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
use KWCMS\modules\Admin\Shared\SimplifiedPager;


/**
 * Class UserTable
 * @package KWCMS\modules\Chsett\Lib
 */
class UserTable
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
    /** @var Table|null */
    protected $table = null;

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
     * @throws ConnectException
     * @throws FormsException
     * @throws TableException
     * @throws AuthException
     * @throws LockException
     */
    public function getTable()
    {
        // full table init
        $this->table = new Table();
        $inputVariables = new Adapters\InputVarsAdapter($this->variables);
        $form = new Form('filterForm');
        $this->table->addHeaderFilter(new KwFilter($form));
        $form->setInputs($inputVariables);

        // order links
        $this->table->addOrder(new Order(new Handler(new Sources\Inputs($this->variables))));

        // pager
        $pager = new BasicPager();
        $pageLink = new PageLink(new Handler(new Sources\Inputs($this->variables)), $pager);
        $pager->setActualPage($pageLink->getPageNumber());
        $this->table->addPager(new SimplifiedPager(new Positions($pager), $pageLink));

        // now normal code - columns
        $this->table->addOrdering('id', IQueryBuilder::ORDER_DESC);

        $this->table->setDefaultHeaderFilterFieldAttributes(['style' => 'width:90%']);

        $columnUserId = new Columns\Func('id', [$this, 'idLink']);
        $columnUserId->style('width:40px', new Rules\Always());
        $this->table->addOrderedColumn(Lang::get('chsett.table.id'), $columnUserId );

        $this->table->addOrderedColumn(Lang::get('chsett.table.login'), new Columns\Basic('login'), new Fields\TextContains());
        $this->table->addOrderedColumn(Lang::get('chsett.table.dir'), new Columns\Basic('dir'), new Fields\TextContains());

        $groups = $this->getGroups();
        $this->table->addOrderedColumn(Lang::get('chsett.table.group'), new Columns\Map('group', $groups), new Fields\Options($groups));
        $classes = $this->libAuth->readClasses();
        $this->table->addOrderedColumn(Lang::get('chsett.table.class'), new Columns\Map('class', $classes), new Fields\Options($classes));
        $this->table->addOrderedColumn(Lang::get('chsett.table.name'), new Columns\Bold('name'), new Fields\TextContains());

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'id');
        $columnActions->addColumn(new Columns\Func('id', [$this, 'editLink']));
        $columnActions->addColumn(new Columns\Func('id', [$this, 'deleteLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $this->table->addColumn(Lang::get('chsett.table.actions'), $columnActions);

        $pager->setLimit(10);
        $this->table->addDataSetConnector(new ConnectUserArray($this->libAuth->readAccounts(), 'id'));
        $this->table->setOutput(new KwRenderer($this->table));
        return $this->table;
    }

    /**
     * @return array
     * @throws AuthException
     * @throws LockException
     */
    protected function getGroups(): array
    {
        $groups = $this->libAuth->readGroup();
        return array_combine(array_map([$this, 'getGroupId'], $groups), array_map([$this, 'getGroupName'], $groups));
    }

    public function getGroupId(IGroup $group): int
    {
        return $group->getGroupId();
    }

    public function getGroupName(IGroup $group): string
    {
        return $group->getGroupName();
    }

    public function idLink($id)
    {
        $user = $this->table->getDataSetConnector()->getByKey($id);
        $this->forward->setLink($this->link->linkVariant('chsett/user/edit/?name=' . $user->getValue('login')));
        $this->forward->setForward($this->link->linkVariant('chsett'));
        return sprintf('<a href="%s" class="button">%s</a>',
            $this->forward->getLink(),
            strval($id)
        );
    }

    public function editLink($id)
    {
        $user = $this->table->getDataSetConnector()->getByKey($id);
        $this->forward->setLink($this->link->linkVariant('chsett/user/edit/?name=' . $user->getValue('login')));
        $this->forward->setForward($this->link->linkVariant('chsett'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x1F589; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.edit_user')
        );
    }

    public function deleteLink($id)
    {
        $user = $this->table->getDataSetConnector()->getByKey($id);
        $this->forward->setLink($this->link->linkVariant('chsett/user/delete/?name=' . $user->getValue('login')));
        $this->forward->setForward($this->link->linkVariant('chsett'));
        return sprintf('<a href="%s" title="%s" class="button button-delete"> &#x1F7AE; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.remove_user')
        );
    }
}
