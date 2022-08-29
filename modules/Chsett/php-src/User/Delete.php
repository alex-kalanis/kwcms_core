<?php

namespace KWCMS\modules\Chsett\User;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Auth;
use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces\IAccessAccounts;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_auth\Interfaces\IAuth;
use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Stored;


/**
 * Class Delete
 * @package KWCMS\modules\Chsett\User
 * Site's users - delete one
 */
class Delete extends AAuthModule
{
    /** @var IAuth|null */
    protected $libAuth = null;
    /** @var IAccessAccounts|null */
    protected $libAccounts = null;
    /** @var IUser|null */
    protected $editUser = null;
    /** @var AuthException|null */
    protected $error = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var Forward */
    protected $forward = null;
    /** @var ExternalLink|null */
    protected $links = null;

    public function __construct()
    {
        Lang::load('Chsett');
        Lang::load('Admin');
        $this->libAuth = Auth::getAuth();
        $this->libAccounts = Auth::getAccounts();
        $this->links = new ExternalLink(Stored::getPath());
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, ];
    }

    public function run(): void
    {
        try {
            $userName = strval($this->getFromParam('name'));
            $this->editUser = $this->libAuth->getDataOnly($userName);
            if (empty($this->editUser)) {
                throw new AuthException(Lang::get('chsett.user_not_found', $userName));
            }
            $this->libAccounts->deleteAccount($this->editUser->getAuthName());
            $this->isProcessed = true;
        } catch (AuthException | LockException $ex) {
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
        $this->forward->setForward($this->links->linkVariant('chsett/groups'));
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
