<?php

namespace KWCMS\modules\Admin;


use kalanis\kw_address_handler\Redirect;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\CookieAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class Login
 * @package KWCMS\modules\Admin
 * Admin login
 */
class Login extends AAuthModule implements IModuleTitle
{
    protected $form = null;

    public function __construct()
    {
        Lang::load('Admin');
        $this->form = new Forms\LoginForm('login');
        $this->form->fill(new CookieAdapter());
    }

    public function process(): void
    {
        parent::process();
        if (!$this->user) {
            try {
                $inputAdapter = new InputVarsAdapter($this->inputs);
                $inputAdapter->loadEntries(InputVarsAdapter::SOURCE_POST);
                $this->form->setInputs($inputAdapter);
                $this->form->process();

            } catch (FormsException $ex) {
                $this->error = $ex;
            }
        }
    }

    protected function run(): void
    {
        // not used, just for api
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function output(): Output\AOutput
    {
        if ($this->error) { // fill form with error say
            $this->form->getControl('login')->addRule(
                IRules::ALWAYS, $this->error->getMessage()
            );
            $this->form->process('login');
        }

        if ($this->user) { // logged in
            $link = new ExternalLink(Config::getPath());
            if ($this->isJson()) {
                // create json with status info
                $out = new Output\Json();
                return $out->setContent([
                    'message' => 'Logged in!',
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
                return $out->setContent('Logged in!');
            }
        } else {
            if ($this->isJson()) {
                $out = new Output\Json();
                return $out->setContent([
                    'message' => 'Login fail',
                    'errors' => $this->form->renderErrorsArray(),
                ]);
            } else {
                $tmpl = new Templates\LoginTemplate();
                $out = new Output\Html();
                return $out->setContent($tmpl->setData($this->form)->render());
            }
        }
    }

    protected function result(): Output\AOutput
    {
        // not used, just for api
        return new Output\Html();
    }

    public function getTitle(): string
    {
        return Lang::get('login.page');
    }
}
