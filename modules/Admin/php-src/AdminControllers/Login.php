<?php

namespace KWCMS\modules\Admin\AdminControllers;


use kalanis\kw_address_handler\Redirect;
use kalanis\kw_auth_sources\Interfaces\IWorkClasses;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_input\Simplified\CookieAdapter;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_langs\Support;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_rules\Interfaces\IRules;
use kalanis\kw_scripts\Scripts;
use KWCMS\modules\Admin\Forms;
use KWCMS\modules\Admin\Templates;


/**
 * Class Login
 * @package KWCMS\modules\Admin\AdminControllers
 * Admin login
 */
class Login extends AAuthModule implements IModuleTitle
{
    protected $form = null;
    protected $cookieAdapter = null;
    protected $sessionAdapter = null;

    public function __construct()
    {
        $this->cookieAdapter = new CookieAdapter();
        $this->sessionAdapter = new SessionAdapter();
        $this->form = new Forms\LoginForm('login');
    }

    /**
     * @throws LangException
     */
    public function process(): void
    {
        try {
            Lang::load('Admin');
            $this->form->fill($this->cookieAdapter, $this->sessionAdapter);
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                parent::process();
            }
        } catch (FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function run(): void
    {
        Support::setToArray(new SessionAdapter(), $this->form->getControl('lang')->getValue());
    }

    public function allowedAccessClasses(): array
    {
        return [IWorkClasses::CLASS_MAINTAINER, IWorkClasses::CLASS_ADMIN, IWorkClasses::CLASS_USER, ];
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        if ($this->error) { // fill form with error say
            $this->form->getControl('login')->addRule(
                IRules::ALWAYS, $this->error->getMessage()
            );
            $this->form->process('login');
        }

        $link = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
        if ($this->user) { // logged in
            if ($this->isJson()) {
                // create json with status info
                $out = new Output\Json();
                return $out->setContent([
                    'message' => Lang::get('login.success'),
                    'name' => $this->user->getDisplayName(),
                    'dir' => $this->user->getDir(),
                    'class' => $this->user->getClass(),
                    'group' => $this->user->getGroup(),
                    'link' => $link->linkVariant(''),
                    'errors' => [],
                ]);
            } else {
                new Redirect($link->linkVariant(''), Redirect::TARGET_TEMPORARY, 5);
                $out = new Output\Html();
                return $out->setContent(Lang::get('login.success'));
            }
        } else {
            if ($this->isJson()) {
                $out = new Output\Json();
                return $out->setContent([
                    'message' => Lang::get('login.fail'),
                    'errors' => $this->form->renderErrorsArray(),
                ]);
            } else {
                $tmpl = new Templates\LoginTemplate();
                Scripts::want('Admin', 'langchange.js');
                $out = new Output\Html();
                return $out->setContent($tmpl->setData(
                    $this->form,
                    $link->linkVariant('lang-change', '', true)
                )->render());
            }
        }
    }

    protected function result(): Output\AOutput
    {
        // not used, just for api
        return new Output\Raw();
    }

    public function getTitle(): string
    {
        return Lang::get('login.page');
    }
}
