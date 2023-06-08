<?php

namespace KWCMS\modules\Files\AdminControllers\File;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputFilesAdapter;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_tree_controls\TWhereDir;
use KWCMS\modules\Files\Lib;


/**
 * Class Upload
 * @package KWCMS\modules\Files\File
 * Upload content
 */
class Upload extends AAuthModule implements IModuleTitle
{
    use Lib\TLibAction;
    use Lib\TModuleTemplate;
    use TWhereDir;

    /** @var Lib\FileForm|null */
    protected $fileForm = null;
    /** @var bool */
    protected $processed = false;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->fileForm = new Lib\FileForm('uploadFileForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        try {
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
                $libAction = $this->getLibAction();
                $usedName = $libAction->findFreeName($file->getValue());
                $this->processed = $libAction->uploadFile($file, $usedName);
            }
        } catch (FilesException | FormsException $ex) {
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
        } catch (FilesException | FormsException $ex) {
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
