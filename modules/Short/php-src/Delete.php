<?php

namespace KWCMS\modules\Short;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stored;
use kalanis\kw_tree_controls\TWhereDir;


/**
 * Class Delete
 * @package KWCMS\modules\Short
 * Site's short messages - delete record
 */
class Delete extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;

    /** @var MapperException|null */
    protected $error = null;
    /** @var UserDir|null */
    protected $userDir = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var Forward */
    protected $forward = null;

    public function __construct()
    {
        $this->initTModuleTemplate();
        Config::load('Short');
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
        $this->userDir = new UserDir(Stored::getPath());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->initWhereDir(new SessionAdapter(), $this->inputs);
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();
            $adapter = new Lib\MessageAdapter($this->userDir->getWebRootDir() . $this->userDir->getHomeDir() . $this->getWhereDir());
            $record = $adapter->getRecord();
            $record->id = strval($this->getFromParam('id'));
            $this->isProcessed = $record->delete();
        } catch (MapperException | ShortException $ex) {
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
            Notification::addSuccess(Lang::get('short.removed'));
        }
        $this->forward->forward();
        $this->forward->setForward($this->links->linkVariant('short/dashboard'));
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

    public function getTitle(): string
    {
        return Lang::get('short.page') . ' - ' . Lang::get('short.remove_record');
    }
}
