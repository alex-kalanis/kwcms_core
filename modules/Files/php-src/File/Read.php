<?php

namespace KWCMS\modules\Files\File;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Processing\Volume\ProcessDir;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stored;
use kalanis\kw_styles\Styles;
use kalanis\kw_tree\Tree;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Files\Lib;


/**
 * Class Read
 * @package KWCMS\modules\Files\File
 * Read content
 */
class Read extends AAuthModule implements IModuleTitle
{
    use Lib\TLibAction;
    use Lib\TModuleTemplate;
    use Lib\TParams;
    use TWhereDir;

    /** @var UserDir|null */
    protected $userDir = null;
    /** @var Tree|null */
    protected $tree = null;
    /** @var Lib\FileForm|null */
    protected $fileForm = null;
    /** @var MimeType|null */
    protected $libFileMime = null;
    /** @var string */
    protected $fileMime = '';
    /** @var string */
    protected $fileContent = '';
    /** @var bool */
    protected $processed = false;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->tree = new Tree(Stored::getPath(), new ProcessDir());
        $this->userDir = new UserDir(Stored::getPath());
        $this->fileForm = new Lib\FileForm('readFileForm');
        $this->libFileMime = new MimeType(true);
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        try {
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->tree->startFromPath($this->userDir->getHomeDir() . $this->getWhereDir());
            $this->tree->canRecursive(false);
            $this->tree->setFilterCallback([$this, 'filterFiles']);
            $this->tree->process();
            $this->fileForm->composeReadFile($this->tree->getTree());
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->fileForm->process()) {
                $item = $this->fileForm->getControl('sourceName')->getValue();
                $this->fileContent = $this->getLibFileAction()->readFile($item);
                $this->fileMime = $this->libFileMime->mimeByPath($item);
                $this->processed = true;
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
            : ($this->isRaw()
                ? $this->outRaw()
                : $this->outHtml()
            );
    }

    public function outHtml(): Output\AOutput
    {
        Styles::want('Files', 'dashboard.css');
        $out = new Output\Html();
        $page = new Lib\ReadTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            return $out->setContent($this->outModuleTemplate($page->setData(
                $this->fileForm,
                Lang::get('files.file.read'),
                $this->fileMime,
                $this->fileContent
            )->render()));
        } catch (FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
    }

    public function outRaw(): Output\AOutput
    {
        $out = new Output\Raw();
        if ($this->error) {
            return $out->setContent($this->error->getMessage());
        } else {
            if ($this->fileContent && $this->fileMime) {
                header('Content-Type: ' . $this->fileMime);
            }
            return $out->setContent($this->fileContent);
        }
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'form_result' => $this->processed,
                'form_errors' => $this->fileForm->renderErrorsArray(),
                'file_mime' => $this->fileMime,
                'file_content_b64' => base64_encode($this->fileContent),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('files.page') . ' - ' . Lang::get('files.file.read.short');
    }
}
