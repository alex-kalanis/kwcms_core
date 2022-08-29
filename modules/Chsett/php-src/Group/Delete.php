<?php

namespace KWCMS\modules\Chsett\Group;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Auth;
use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_auth\Interfaces\IAccessGroups;
use kalanis\kw_auth\Interfaces\IGroup;
use kalanis\kw_auth\Sources\Files;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Stored;


/**
 * Class Delete
 * @package KWCMS\modules\Chsett\Group
 * Group - delete one
 */
class Delete extends AAuthModule
{
    /** @var IAccessGroups|null */
    protected $libGroups = null;
    /** @var IGroup|null */
    protected $group = null;
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
        $this->libGroups = Auth::getGroups();
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
            $groupId = intval(strval($this->getFromParam('id')));
            $this->group = $this->libGroups->getGroupDataOnly($groupId);
            if (empty($this->group)) {
                throw new AuthException(Lang::get('chsett.group_not_found', $groupId));
            }
            $this->libGroups->deleteGroup($this->group->getGroupId());
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
            Notification::addSuccess(Lang::get('chsett.group_removed', $this->group->getGroupName()));
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
