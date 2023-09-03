<?php

namespace KWCMS\modules\Chsett\AdminControllers;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_auth\Auth;
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


/**
 * Class Dashboard
 * @package KWCMS\modules\Chsett\AdminControllers
 * Site's users - list available ones
 */
class Dashboard extends AAuthModule implements IHasTitle
{
    use Templates\TModuleTemplate;

    /**
     * @param mixed ...$constructParams
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, ];
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
            $table = new Lib\UserTable(
                $this->inputs,
                $this->links,
                Auth::getAccounts(),
                Auth::getGroups(),
                Auth::getClasses(),
                $this->user
            );
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
                'users' => Auth::getAccounts()->readAccounts(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('chsett.page');
    }
}
