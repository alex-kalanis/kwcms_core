<?php

namespace KWCMS\modules\Files\AdminControllers\File;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputFilesAdapter;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Files\Lib;


/**
 * Class Upload
 * @package KWCMS\modules\Files\File
 * Upload content
 */
class Upload extends AAuthModule implements IHasTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;

    /** @var Lib\FileForm|null */
    protected $fileForm = null;
    /** @var bool */
    protected $processed = false;
    /** @var UserDir */
    protected $userDir = null;
    /** @var Lib\Processor */
    protected $processor = null;

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        $this->fileForm = new Lib\FileForm('uploadFileForm');
        $files = (new Access\Factory(new FilesTranslations()))->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
        $this->processor = new Lib\Processor($files);
        $this->userDir = new UserDir(new Lib\Translations());
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->getUserDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $workPath = Stuff::linkToArray($this->getWhereDir());

            $this->fileForm->composeUploadFile();
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs), new InputFilesAdapter($this->inputs));
            if ($this->fileForm->process()) {
                $entry = $this->fileForm->getControl('uploadedFile');
                if (!method_exists($entry, 'getFile')) {
                    throw new FilesException(Lang::get('files.error.must_contain_file'));
                }
                $file = $entry->getFile();
                if (!$file instanceof IFileEntry) {
                    throw new FilesException(Lang::get('files.error.must_contain_file'));
                }
                $this->processor->setUserPath($userPath)->setWorkPath($workPath);
                $usedName = $this->processor->findFreeName($file->getValue());
                $this->processed = $this->processor->uploadFile($file, $usedName);
            }
        } catch (FilesException | FormsException | PathsException $ex) {
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
        $page = new Lib\UploadTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            if ($this->processed) {
                Notification::addSuccess(Lang::get('files.uploaded', $this->fileForm->getControl('uploadedFile')->getValue()));
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
        return Lang::get('files.page') . ' - ' . Lang::get('files.file.upload.short');
    }
}
