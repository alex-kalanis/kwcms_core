<?php

namespace KWCMS\modules\Chsett\AdminControllers;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_table\core\TableException;
use KWCMS\modules\Chsett\Lib;
use KWCMS\modules\Chsett\Templates;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class Dashboard
 * @package KWCMS\modules\Chsett\AdminControllers
 * Site's users - list available ones
 */
class Dashboard extends AAuthModule implements IHasTitle
{
    use Templates\TModuleTemplate;

    protected Interfaces\IProcessGroups $libGroups;
    protected Interfaces\IProcessClasses $libClasses;
    /** @var Interfaces\IProcessAccounts|Interfaces\IAuthCert */
    protected $libAccounts = null;

    /**
     * @param Interfaces\IProcessGroups $groups
     * @param Interfaces\IProcessClasses $classes
     * @param Interfaces\IProcessAccounts $accounts
     * @param ExternalLink $external
     * @throws LangException
     */
    public function __construct(
        Interfaces\IProcessGroups $groups,
        Interfaces\IProcessClasses $classes,
        Interfaces\IProcessAccounts $accounts,
        ExternalLink $external
    ) {
        $this->initTModuleTemplate($external);
        $this->libGroups = $groups;
        $this->libClasses = $classes;
        $this->libAccounts = $accounts;
    }

    public function allowedAccessClasses(): array
    {
        return [Interfaces\IProcessClasses::CLASS_MAINTAINER, Interfaces\IProcessClasses::CLASS_ADMIN, ];
    }

    public function run(): void
    {
    }

    /**
     * @throws AccountsException
     * @return Output\AOutput
     */
    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        if (!empty($this->error)) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        }
        try {
            $table = new Lib\UserTable($this->inputs, $this->links, $this->libAccounts, $this->libGroups, $this->libClasses, $this->user);
            return $out->setContent($this->outModuleTemplate($table->getTable()->render()));
        } catch ( AccountsException | ConnectException | FormsException | LangException | TableException $ex) {
            return $out->setContent($this->outModuleTemplate($ex->getMessage() . nl2br($ex->getTraceAsString())));
        }
    }

    /**
     * @throws AccountsException
     * @return Output\AOutput
     */
    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'users' => $this->libAccounts->readAccounts(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('chsett.page');
    }
}
