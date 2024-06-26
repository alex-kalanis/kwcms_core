<?php

namespace KWCMS\modules\Chsett\AdminControllers\Group;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\AuthException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class Delete
 * @package KWCMS\modules\Chsett\AdminControllers\Group
 * Group - delete one
 */
class Delete extends AAuthModule
{
    protected Interfaces\IProcessGroups $libGroups;
    protected ?Interfaces\IGroup $group = null;
    protected ?AuthException $error = null;
    protected bool $isProcessed = false;
    protected Forward $forward;
    protected ExternalLink $links;

    /**
     * @param Interfaces\IProcessGroups $groups
     * @param Forward $forward
     * @param ServerRequest $request
     * @param ExternalLink $external
     * @throws LangException
     * @throws HandlerException
     */
    public function __construct(
        Interfaces\IProcessGroups $groups,
        Forward $forward,
        ServerRequest $request,
        ExternalLink $external
    ) {
        Lang::load('Chsett');
        Lang::load('Admin');
        $this->libGroups = $groups;
        $this->links = $external;
        $this->forward = $forward;
        $this->forward->setSource($request);
    }

    public function allowedAccessClasses(): array
    {
        return [Interfaces\IProcessClasses::CLASS_MAINTAINER, ];
    }

    public function run(): void
    {
        try {
            $groupId = intval(strval($this->getFromParam('id')));
            $this->group = $this->libGroups->getGroupDataOnly($groupId);
            if (empty($this->group)) {
                throw new AccountsException(Lang::get('chsett.group_not_found', $groupId));
            }
            $this->isProcessed = $this->libGroups->deleteGroup($this->group->getGroupId());
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
