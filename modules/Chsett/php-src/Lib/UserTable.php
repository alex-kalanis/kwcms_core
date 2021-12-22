<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_auth\Interfaces\IGroup;
use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_auth\Sources\Files;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Adapters;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Form;
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
use kalanis\kw_table\core\Table\Sorter;
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
     */
    public function getTable()
    {
        // full table init
        $table = new Table();
        $inputVariables = new Adapters\InputVarsAdapter($this->variables);
        $form = new Form('filterForm');
        $table->addHeaderFilter(new KwFilter($form));
        $form->setInputs($inputVariables);

        // sorter links
        $sorter = new Sorter(new Handler(new Sources\Inputs($this->variables)));
        $table->addSorter($sorter);

        // pager
        $pager = new BasicPager();
        $pageLink = new PageLink(new Handler(new Sources\Inputs($this->variables)), $pager);
        $pager->setActualPage($pageLink->getPageNumber());
        $table->addPager(new SimplifiedPager(new Positions($pager), $pageLink));

        // now normal code - columns
        $table->setDefaultSorting('id', IQueryBuilder::ORDER_DESC);

        $table->setDefaultHeaderFilterFieldAttributes(['style' => 'width:90%']);

        $columnUserId = new Columns\Func('id', [$this, 'idLink']);
        $columnUserId->style('width:40px', new Rules\Always());
        $table->addSortedColumn(Lang::get('chsett.id'), $columnUserId );

        $table->addSortedColumn(Lang::get('chsett.login'), new Columns\Basic('login'), new Fields\TextContains());
        $table->addSortedColumn(Lang::get('chsett.dir'), new Columns\Basic('dir'), new Fields\TextContains());

        $groups = $this->getGroups();
        $table->addSortedColumn(Lang::get('chsett.group'), new Columns\Map('group', $groups), new Fields\Options($groups));
        $classes = $this->libAuth->readClasses();
        $table->addSortedColumn(Lang::get('chsett.class'), new Columns\Map('class', $classes), new Fields\Options($classes));
        $table->addSortedColumn(Lang::get('chsett.name'), new Columns\Bold('name'), new Fields\TextContains());

        $columnActions = new Columns\Multi('&nbsp;&nbsp;', 'id');
        $columnActions->addColumn(new Columns\Func('id', [$this, 'editLink']));
        $columnActions->addColumn(new Columns\Func('id', [$this, 'deleteLink']));
        $columnActions->style('width:100px', new Rules\Always());

        $table->addColumn(Lang::get('chsett.actions'), $columnActions);

        $pager->setLimit(10);
        $table->addDataSetConnector(new ConnectUserArray($this->libAuth->readAccounts()));
        $table->setOutput(new KwRenderer($table));
        return $table;
    }

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
        $this->forward->setLink($this->link->linkVariant('chsett/edit/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('chsett'));
        return sprintf('<a href="%s" class="button">%s</a>',
            $this->forward->getLink(),
            strval($id)
        );
    }

    public function editLink($id)
    {
        $this->forward->setLink($this->link->linkVariant('chsett/edit/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('chsett'));
        return sprintf('<a href="%s" title="%s" class="button button-edit"> &#x1F589; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.update_user')
        );
    }

    public function deleteLink($id)
    {
        $this->forward->setLink($this->link->linkVariant('chsett/delete/?id=' . $id));
        $this->forward->setForward($this->link->linkVariant('chsett'));
        return sprintf('<a href="%s" title="%s" class="button button-delete"> &#x1F7AE; </a>',
            $this->forward->getLink(),
            Lang::get('chsett.remove_user')
        );
    }
}
