<?php

namespace KWCMS\modules\Chsett\AdminControllers\User;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Auth;
use kalanis\kw_auth\AuthException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class Delete
 * @package KWCMS\modules\Chsett\AdminControllers\User
 * Site's users - delete one
 */
class Delete extends AAuthModule
{
    /** @var Interfaces\IAuth|null */
    protected $libAuth = null;
    /** @var Interfaces\IProcessAccounts|null */
    protected $libAccounts = null;
    /** @var Interfaces\IUser|null */
    protected $editUser = null;
    /** @var AuthException|null */
    protected $error = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var Forward */
    protected $forward = null;
    /** @var ExternalLink|null */
    protected $links = null;

    /**
     * @param mixed ...$constructParams
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        Lang::load('Chsett');
        Lang::load('Admin');
        $this->libAuth = Auth::getAuth();
        $this->libAccounts = Auth::getAccounts();
        $this->links = new ExternalLink(StoreRouted::getPath());
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [Interfaces\IProcessClasses::CLASS_MAINTAINER, ];
    }

    public function run(): void
    {
        try {
            $userName = strval($this->getFromParam('name'));
            $this->editUser = $this->libAuth->getDataOnly($userName);
            if (empty($this->editUser)) {
                throw new AccountsException(Lang::get('chsett.user_not_found', $userName));
            }
            $this->libAccounts->deleteAccount($this->editUser->getAuthName());
            $this->isProcessed = true;
        } catch (AccountsException $ex) {
            $this->error = $ex;
        }
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        if ($this->isProcessed) {
            Notification::addSuccess(Lang::get('chsett.user_removed', $this->editUser->getDisplayName()));
        }
        $this->forward->forward();
        $this->forward->setForward($this->links->linkVariant(['chsett', 'groups']));
        $this->forward->forward();
        return new Output\Raw();
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            return $out->setContent(['Success']);
        }
    }
}
