<?php

namespace KWCMS\modules\Core\Libs;


use kalanis\kw_accounts\Interfaces\IUser;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Headers;
use kalanis\kw_address_handler\Redirect;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Auth;
use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces\IAuthTree;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_locks\LockException;
use kalanis\kw_modules\Output;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Interfaces\Modules\IHasUser;


/**
 * Class AAuthModule
 * @package KWCMS\modules\Core\Libs
 * Basic class for each authorized module
 *
 * __construct() is for DI/class building
 * run() is for that hard work when logged in
 * result() is for getting output class
 */
abstract class AAuthModule extends AModule implements IHasUser
{
    /** @var IUser|null */
    protected $user = null;
    /** @var AuthException|LockException|null */
    protected $error = null;

    /**
     * Overload only if you need to access content without logging in (like on login and logout)
     */
    public function process(): void
    {
        try {
            if (!$this->inputs) {
                throw new AuthException('Set inputs first!');
            }
            $authTree = $this->getAuthTree();
            if (!$authTree) {
                throw new AuthException('No classes set to authorize against!');
            }
            $authTree->findMethod($this->inputs->getInObject(null, $this->getPossibleSources()));
            if ($authTree->getMethod() && $authTree->getMethod()->isAuthorized()) {
                $this->user = $authTree->getMethod()->getLoggedUser();
                if ($this->user && in_array($this->user->getClass(), $this->allowedAccessClasses())) {
                    $this->run();
                } else {
                    throw new AuthException('Restricted access', 405);
                }
            }
        } catch (AuthException | LockException $ex) {
            $this->error = $ex;
        }
    }

    protected function getAuthTree(): ?IAuthTree
    {
        return Auth::getTree();
    }

    /**
     * @return string[]
     */
    protected function getPossibleSources(): array
    {
        return [IEntry::SOURCE_EXTERNAL, IEntry::SOURCE_CLI, IEntry::SOURCE_POST, IEntry::SOURCE_GET];
    }

    /**
     * Process things
     */
    abstract protected function run(): void;

    /**
     * Which users can do anything in that module?
     * @return int[]
     */
    abstract protected function allowedAccessClasses(): array;

    /**
     * Overload only if you need to access content without logging in (like on login and logout)
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        if ($this->user) {
            return $this->result();
        } elseif ($this->error) {
            $code = $this->error->getCode() ? intval($this->error->getCode()) : 401 ;
            $this->customHeader($code);
            if ($this->isJson()) {
                $output = new Output\JsonError();
                return $output->setContent($code, $this->error->getMessage());
            } elseif ($this->isRaw()) {
                $output = new Output\Raw();
                return $output->setContent($this->error->getMessage());
            } else {
                $output = new Output\Html();
                return $output->setContent(sprintf('<h1>%s</h1>', $this->error->getMessage()));
            }
        } else {
            if ($this->isJson()) {
                $output = new Output\JsonError();
                return $output->setContent(401, 'Authorize first');
            } elseif ($this->isRaw()) {
                $output = new Output\Raw();
                return $output->setContent('Authorize first');
            } else {
                $forward = new Forward();
                $forward->setLink((new ExternalLink(StoreRouted::getPath()))->linkVariant(['login']));
                $forward->setForward((new ServerRequest())->getAddress());
                new Redirect($forward->getLink(), Redirect::TARGET_TEMPORARY, 5);

                $output = new Output\Html();
                return $output->setContent(sprintf('<h1>%s</h1>', 'Authorize first'));
            }
        }
    }

    protected function customHeader(int $code): void
    {
        $protocol = $this->inputs->getInArray('SERVER_PROTOCOL', [IEntry::SOURCE_SERVER]);
        Headers::setCustomCode(strval(reset($protocol)), $code);
    }

    /**
     * What will be answered
     * @return Output\AOutput
     */
    abstract protected function result(): Output\AOutput;

    public function getUser(): ?IUser
    {
        return $this->user;
    }
}
