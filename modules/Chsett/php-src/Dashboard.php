<?php

namespace KWCMS\modules\Chsett;


use kalanis\kw_auth\Auth;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_table\core\TableException;


/**
 * Class Dashboard
 * @package KWCMS\modules\Chsett
 * Site's users - list available ones
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Templates\TModuleTemplate;

    public function __construct()
    {
        $this->initTModuleTemplate();
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, ];
    }

    public function run(): void
    {
    }

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
        } catch ( FormsException | TableException | ConnectException $ex) {
            return $out->setContent($this->outModuleTemplate($ex->getMessage() . nl2br($ex->getTraceAsString())));
        }
    }

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
