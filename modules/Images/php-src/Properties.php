<?php

namespace KWCMS\modules\Images;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Admin\Shared;


/**
 * Class Properties
 * @package KWCMS\modules\Images
 * Directory properties - description, thumb
 */
class Properties extends AAuthModule implements IModuleTitle
{
    use Lib\TLibAction;
    use Templates\TModuleTemplate;
    use TWhereDir;

    /** @var Forms\DescForm|null */
    protected $descForm = null;
    /** @var Forms\DirExtraForm|null */
    protected $extraForm = null;
    /** @var bool */
    protected $hasExtra = false;
    /** @var bool */
    protected $processed = false;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->descForm = new Forms\DescForm('dirPropsForm');
        $this->extraForm = new Forms\DirExtraForm('dirPropsForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        try {
            $libAction = $this->getLibDirAction();
            $this->hasExtra = $libAction->canUse();
            if ($this->hasExtra) {
                $this->descForm->composeForm($libAction->getDesc(),'#');
                $this->descForm->setInputs(new InputVarsAdapter($this->inputs));
                if ($this->descForm->process()) {
                    $this->processed = $libAction->updateDesc(strval($this->descForm->getControl('description')->getValue()));
                }
            } else {
                $this->extraForm->composeForm('#');
                $this->extraForm->setInputs(new InputVarsAdapter($this->inputs));
                if ($this->extraForm->process()) {
                    $this->processed = $libAction->createExtra();
                }
            }
        } catch (ImagesException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getUserDir(): string
    {
        return $this->user->getDir();
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Shared\FillHtml($this->user);
        $extraPage = new Templates\DirExtraTemplate();
        $descPage = new Templates\DirDescTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            if ($this->processed) {
                if ($this->hasExtra) {
                    Notification::addSuccess(Lang::get('images.props_updated'));
                } else {
                    Notification::addSuccess(Lang::get('images.dirs_created'));
                }
            }
            return $out->setContent($this->outModuleTemplate(
                $this->hasExtra
                    ? $descPage->setData($this->descForm)->render()
                    : $extraPage->setData($this->extraForm)->render()
            ));
        } catch (FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'has_extra' => intval($this->hasExtra),
                'form_result' => intval($this->processed),
                'form_errors' => $this->hasExtra ? $this->descForm->renderErrorsArray() : $this->extraForm->renderErrorsArray(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('images.page') . ' - ' . Lang::get('images.dir_props.short');
    }
}
