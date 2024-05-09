<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\HandlerException;
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
use kalanis\kw_paths\PathsException;
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
 * Class UserTable
 * @package KWCMS\modules\Chsett\Lib
 */
class UserTable
{
    use TStatuses;

    protected IFiltered $variables;
    protected ExternalLink $link;
    protected Interfaces\IProcessAccounts $libAccounts;
    protected Interfaces\IProcessGroups $libGroups;
    protected Interfaces\IProcessClasses $libClasses;
    protected Interfaces\IUser $currentUser;
    protected Forward $forward;
    protected Table $table;

    public function __construct(IFiltered $inputs, ExternalLink $link, Interfaces\IProcessAccounts $libAccounts, Interfaces\IProcessGroups $libGroups, Interfaces\IProcessClasses $libClasses, Interfaces\IUser $currentUser)
    {
        $this->variables = $inputs;
        $this->link = $link;
        $this->libAccounts = $libAccounts;
        $this->libGroups = $libGroups;
        $this->libClasses = $libClasses;
        $this->currentUser = $currentUser;
        $this->forward = new Forward();
        $this->table = new Table();
    }

    /**
     * @throws AccountsException
     * @throws FormsException
     * @throws HandlerException
     * @throws LangException
     * @throws TableException
     * @return Table
     */
    public function getTable()
    {
        // full table init
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
        $classes = $this->libClasses->readClasses();
        $this->table->addOrderedColumn(Lang::get('chsett.table.class'), new Columns\Map('class', $classes), new Fields\Options($classes));
        $this->table->addOrderedColumn(Lang::get('chsett.table.name'), new Columns\Bold('name'), new Fields\TextContains());
        $statuses = $this->statuses();
        $this->table->addOrderedColumn(Lang::get('chsett.table.status'), new Columns\Map('status', $statuses), new Fields\Options($statuses));

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'id');
        $columnActions->addColumn(new Columns\Func('id', [$this, 'editLink']));
        $columnActions->addColumn(new Columns\Func('id', [$this, 'deleteLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $this->table->addColumn(Lang::get('chsett.table.actions'), $columnActions);

        $pager->setLimit(10);
        $this->table->addDataSetConnector(new ConnectUserArray($this->libAccounts->readAccounts(), 'id'));
        $this->table->setOutput(new KwRenderer($this->table));
        return $this->table;
    }

    /**
     * @throws AccountsException
     * @return array<string, string>
     */
    protected function getGroups(): array
    {
        $groups = $this->libGroups->readGroup();
        return array_combine(array_map([$this, 'getGroupId'], $groups), array_map([$this, 'getGroupName'], $groups));
    }

    public function getGroupId(Interfaces\IGroup $group): int
    {
        return $group->getGroupId();
    }

    public function getGroupName(Interfaces\IGroup $group): string
    {
        return $group->getGroupName();
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
        $user = $this->table->getDataSetConnector()->getByKey($id);
        $this->forward->setLink($this->link->linkVariant('chsett/user/edit/?name=' . $user->getValue('login')));
        $this->forward->setForward($this->link->linkVariant('chsett'));
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
    public function editLink($id): string
    {
        $user = $this->table->getDataSetConnector()->getByKey($id);
        $this->forward->setLink($this->link->linkVariant('chsett/user/edit/?name=' . $user->getValue('login')));
        $this->forward->setForward($this->link->linkVariant('chsett'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x1F589; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.edit_user')
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
        $user = $this->table->getDataSetConnector()->getByKey($id);
        $this->forward->setLink($this->link->linkVariant('chsett/user/delete/?name=' . $user->getValue('login')));
        $this->forward->setForward($this->link->linkVariant('chsett'));
        return sprintf('<a href="%s" title="%s" class="button button-delete"> &#x1F7AE; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.remove_user')
        );
    }
}
