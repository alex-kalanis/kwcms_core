<?php

namespace KWCMS\modules\Images;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_tree\Tree;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Admin\Shared;


/**
 * Class Edit
 * @package KWCMS\modules\Images
 * File edit - that page
 */
class Edit extends AAuthModule implements IModuleTitle
{
    use Lib\TLibAction;
    use Lib\TLibFilters;
    use Templates\TModuleTemplate;
    use TWhereDir;

    /** @var Forms\FileRenameForm|null */
    protected $renameForm = null;
    /** @var Forms\DescForm|null */
    protected $descForm = null;
    /** @var Forms\FileThumbForm|null */
    protected $thumbForm = null;
    /** @var Forms\FileActionForm|null */
    protected $moveForm = null;
    /** @var Forms\FileActionForm|null */
    protected $copyForm = null;
    /** @var Forms\FileThumbForm|null */
    protected $primaryForm = null;
    /** @var Forms\FileDeleteForm|null */
    protected $deleteForm = null;
    /** @var UserDir|null */
    protected $userDir = null;
    /** @var Tree|null */
    protected $tree = null;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->renameForm = new Forms\FileRenameForm('fileNameForm');
        $this->descForm = new Forms\DescForm('fileDescForm');
        $this->thumbForm = new Forms\FileThumbForm('fileThumbForm');
        $this->moveForm = new Forms\FileActionForm('fileMoveForm');
        $this->copyForm = new Forms\FileActionForm('fileCopyForm');
        $this->primaryForm = new Forms\FileThumbForm('filePrimaryForm');
        $this->deleteForm = new Forms\FileDeleteForm('fileDeleteForm');
        $this->tree = new Tree(Config::getPath());
        $this->userDir = new UserDir(Config::getPath());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        try {
            $fileName = strval($this->getFromParam('name'));
            // no name or invalid file name -> redirect!
            $libAction = $this->getLibFileAction();

            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();
            $this->tree->canRecursive(true);
            $this->tree->startFromPath($this->userDir->getHomeDir());
            $this->tree->setFilterCallback([$this, 'filterDirs']);
            $this->tree->process();

            // target links are redirects - action outside and then response somewhere (not necessary here)
            $this->renameForm->composeForm($fileName);
            $this->descForm->composeForm($libAction->readDesc($fileName), '#');
            $this->thumbForm->composeForm('#');
            $this->moveForm->composeForm($this->tree->getTree(),'#');
            $this->copyForm->composeForm($this->tree->getTree(),'#');
            $this->primaryForm->composeForm('#');
            $this->deleteForm->composeForm('#');

//            $this->thumbPath = $libAction->;
//            'thumb' => $libGallery->getLibThumb()->getPath($whereDir . DIRECTORY_SEPARATOR . $fileName),

        } catch (ImagesException $ex) {
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
        $page = new Templates\SingleTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            return $out->setContent($this->outModuleTemplate($page->setData(
                '#',
                '#',
                $this->thumbForm,
                $this->descForm,
                $this->renameForm,
                $this->copyForm,
                $this->moveForm,
                $this->primaryForm,
                $this->deleteForm
            )->render()));
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
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('images.page') . ' - ' . Lang::get('images.files_props.short');
    }
}
