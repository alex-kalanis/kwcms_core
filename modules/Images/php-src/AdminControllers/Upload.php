<?php

namespace KWCMS\modules\Images\AdminControllers;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputFilesAdapter;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Images\Lib;
use KWCMS\modules\Images\Forms;
use KWCMS\modules\Images\Templates;


/**
 * Class Upload
 * @package KWCMS\modules\Images\AdminControllers
 * Upload image
 */
class Upload extends AAuthModule implements IModuleTitle
{
    use Lib\TLibAction;
    use Templates\TModuleTemplate;
    use TWhereDir;

    /** @var Forms\FileUploadForm */
    protected $fileForm = null;
    /** @var UserDir */
    protected $userDir = null;
    /** @var bool */
    protected $processed = false;

    /**
     * @throws LangException
     */
    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->fileForm = new Forms\FileUploadForm('uploadImageForm');
        $this->userDir = new UserDir();
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    /**
     * @throws FilesException
     * @throws PathsException
     */
    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $currentPath = Stuff::linkToArray($this->getWhereDir());

            $this->fileForm->composeForm();
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs), new InputFilesAdapter($this->inputs));
            if ($this->fileForm->process()) {
                $entry = $this->fileForm->getControl('uploadedFile');
                if (!method_exists($entry, 'getFile')) {
                    throw new ImagesException(Lang::get('images.error.must_contain_file'));
                }
                $file = $entry->getFile();
                if (!$file instanceof IFileEntry) {
                    throw new ImagesException(Lang::get('images.error.must_contain_file'));
                }
                $libAction = $this->getLibFileAction($userPath, $currentPath);
                $usedName = $libAction->findFreeName($file->getValue());
                $this->processed = $libAction->uploadFile(
                    $file,
                    $usedName,
                    strval($this->fileForm->getControl('description')->getValue())
                );
            }
        } catch (ImagesException | FormsException | FilesException | PathsException | MimeException $ex) {
            if (isset($usedName) && isset($libAction)) {
                $libAction->deleteFile($usedName);
            }
            $this->error = $ex;
        }
    }

    protected function getUserDir(): string
    {
        return $this->user->getDir();
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
        $page = new Templates\UploadTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            if ($this->processed) {
                Notification::addSuccess(Lang::get('images.uploaded', $this->fileForm->getControl('uploadedFile')->getValue()));
            }
            return $out->setContent($this->outModuleTemplate($page->setData($this->fileForm)->render()));
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
                'form_result' => intval($this->processed),
                'form_errors' => $this->fileForm->renderErrorsArray(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('images.page') . ' - ' . Lang::get('images.upload.short');
    }
}