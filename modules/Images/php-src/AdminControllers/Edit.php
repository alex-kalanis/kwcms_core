<?php

namespace KWCMS\modules\Images\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree\Traits\TFilesDirs;
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
 * Class Edit
 * @package KWCMS\modules\Images\AdminControllers
 * File edit - that page
 */
class Edit extends AAuthModule implements IHasTitle
{
    use Lib\TLibAction;
    use Lib\TLibExistence;
    use Templates\TModuleTemplate;
    use TWhereDir;
    use TFilesDirs;

    protected string $fileName = '';
    protected Access\CompositeAdapter $files;
    protected Forms\FileRenameForm $renameForm;
    protected Forms\DescForm $descForm;
    protected Forms\FileThumbForm $thumbForm;
    protected Forms\FileActionForm $moveForm;
    protected Forms\FileActionForm $copyForm;
    protected Forms\FileThumbForm $primaryForm;
    protected Forms\FileDeleteForm $deleteForm;
    protected Lib\ProcessFile $libAction;
    protected UserDir $userDir;
    protected ITree $tree;
    protected Forward $forward;
    protected bool $redirect = false;

    protected $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws LangException
     * @throws FilesException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        $this->renameForm = new Forms\FileRenameForm('fileNameForm');
        $this->descForm = new Forms\DescForm('fileDescForm');
        $this->thumbForm = new Forms\FileThumbForm('fileThumbForm');
        $this->moveForm = new Forms\FileActionForm('fileMoveForm');
        $this->copyForm = new Forms\FileActionForm('fileCopyForm');
        $this->primaryForm = new Forms\FileThumbForm('filePrimaryForm');
        $this->deleteForm = new Forms\FileDeleteForm('fileDeleteForm');
        $this->files = (new Access\Factory(new FilesTranslations()))->getClass($constructParams);
        $this->tree = new DataSources\Files($this->files);
        $this->userDir = new UserDir(new Lib\Translations());
        $this->forward = new Forward();
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
            $this->fileName = strval($this->getFromParam('name'));
            // no name or invalid file name -> redirect!

            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $currentPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->libAction = $this->getLibFileAction($this->constructParams, $userPath, $currentPath);
            $this->checkExistence($this->libAction->getLibImage(), array_merge($userPath, $currentPath), $this->fileName);

            $this->tree->wantDeep(true);
            $this->tree->setStartPath($userPath);
            $this->tree->setFilterCallback([$this, 'justDirsCallback']);
            $this->tree->process();

            // target links are redirects - action outside and then response somewhere (not necessary here)
            $this->forward->setLink($this->links->linkVariant('images/edit/thumb?name='. $this->fileName))
                ->setForward($this->links->linkVariant('images/edit?name='. $this->fileName));
            $this->thumbForm->composeForm($this->forward->getLink());

            $this->forward->setLink($this->links->linkVariant('images/edit/desc?name='. $this->fileName))
                ->setForward($this->links->linkVariant('images/edit?name='. $this->fileName));
            $this->descForm->composeForm($this->libAction->readDesc(
                $this->getWhereDir() . DIRECTORY_SEPARATOR . $this->fileName
            ), $this->forward->getLink());

            $this->forward->setLink($this->links->linkVariant('images/edit/rename?name='. $this->fileName))
                ->setForward('');
            $this->renameForm->composeForm($this->fileName, $this->forward->getLink());

            $this->forward->setLink($this->links->linkVariant('images/edit/move?name='. $this->fileName))
                ->setForward('');
            $this->moveForm->composeForm($this->tree->getRoot(), $this->forward->getLink());

            $this->forward->setLink($this->links->linkVariant('images/edit/copy?name='. $this->fileName))
                ->setForward('');
            $this->copyForm->composeForm($this->tree->getRoot(), $this->forward->getLink());

            $this->forward->setLink($this->links->linkVariant('images/edit/delete?name='. $this->fileName))
                ->setForward($this->links->linkVariant('images/dashboard'));
            $this->deleteForm->composeForm($this->forward->getLink());

            $this->forward->setLink($this->links->linkVariant('images/edit/primary?name='. $this->fileName))
                ->setForward($this->links->linkVariant('images/edit?name='. $this->fileName));
            $this->primaryForm->composeForm($this->forward->getLink());

        } catch (ImagesException | FilesException | FormsException | HandlerException | PathsException $ex) {
            $this->redirect = true;
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
        $out = new Output\Html();
        $page = new Templates\SingleTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        if ($this->redirect) {
            $this->forward->setSource(new ServerRequest());
            $this->forward->setForward($this->links->linkVariant('images/dashboard'));
            $this->forward->forward();
        }
        try {
            return $out->setContent($this->outModuleTemplate($page->setData(
                $this->links->linkVariant($this->libAction->reverseImage($this->fileName), 'image', true, false),
                $this->links->linkVariant($this->libAction->reverseThumb($this->fileName), 'image', true, false),
                $this->thumbForm,
                $this->descForm,
                $this->renameForm,
                $this->copyForm,
                $this->moveForm,
                $this->primaryForm,
                $this->deleteForm
            )->render()));
        } catch (FormsException | FilesException | PathsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate(
            $this->error->getMessage() . nl2br($this->error->getTraceAsString())
        ));
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('images.page') . ' - ' . Lang::get('images.files_props.short');
    }
}
