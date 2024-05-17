<?php

namespace KWCMS\modules\Images\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
use KWCMS\modules\Images\Lib;
use KWCMS\modules\Images\Forms;
use KWCMS\modules\Images\Templates;


/**
 * Class Properties
 * @package KWCMS\modules\Images\AdminControllers
 * Directory properties - description, thumb
 */
class Properties extends AAuthModule implements IHasTitle
{
    use Lib\TLibAction;
    use Templates\TModuleTemplate;
    use TWhereDir;

    protected Access\CompositeAdapter $files;
    protected Forms\DescForm $descForm;
    protected Forms\DirExtraForm $extraForm;
    protected UserDir $userDir;
    protected bool $hasExtra = false;
    protected bool $processed = false;

    protected $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        $this->files = (new Access\Factory(new FilesTranslations()))->getClass($constructParams);
        $this->descForm = new Forms\DescForm('dirPropsForm');
        $this->extraForm = new Forms\DirExtraForm('dirPropsForm');
        $this->userDir = new UserDir(new Lib\Translations());
        $this->initLibAction(new images_factory(
            $this->files,
            null,
            null,
            null,
            new ImagesTranslations()
        ));
        $this->constructParams = $constructParams;
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $currentPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $libAction = $this->getLibDirAction($this->constructParams, $userPath, $currentPath);
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
        } catch (ImagesException | FormsException | FilesException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
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
                empty($this->error) ? (
                    $this->hasExtra
                        ? $descPage->setData($this->descForm)->render()
                        : $extraPage->setData($this->extraForm)->render()
                ) : ''
            ));
        } catch (FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
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
