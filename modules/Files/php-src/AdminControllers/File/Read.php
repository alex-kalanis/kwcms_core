<?php

namespace KWCMS\modules\Files\AdminControllers\File;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_styles\Styles;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree\Traits\TFilesFile;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Files\Lib;


/**
 * Class Read
 * @package KWCMS\modules\Files\AdminControllers\File
 * Read content
 */
class Read extends AAuthModule implements IHasTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;
    use TFilesFile;

    /** @var UserDir */
    protected $userDir = null;
    /** @var ITree */
    protected $tree = null;
    /** @var Lib\Processor */
    protected $processor = null;
    /** @var Lib\FileForm */
    protected $fileForm = null;
    /** @var IMime */
    protected $libFileMime = null;
    /** @var string */
    protected $fileMime = '';
    /** @var string */
    protected $fileContent = '';
    /** @var bool */
    protected $processed = false;

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        $files = (new Access\Factory(new FilesTranslations()))->getClass($constructParams);
        $this->tree = new DataSources\Files($files);
        $this->processor = new Lib\Processor($files);
        $this->userDir = new UserDir(new Lib\Translations());
        $this->fileForm = new Lib\FileForm('readFileForm');
        $this->libFileMime = (new Check\Factory())->getLibrary($files);
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
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $workPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->tree->setStartPath(array_merge($userPath, $workPath));
            $this->tree->wantDeep(false);
            $this->tree->setFilterCallback([$this, 'justFilesCallback']);
            $this->tree->process();

            $this->fileForm->composeReadFile($this->tree->getRoot());
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->fileForm->process()) {
                $item = $this->fileForm->getControl('sourceName')->getValue();
                $this->processor->setUserPath($userPath)->setWorkPath($workPath);
                $this->fileContent = $this->processor->readFile($item);
                $this->fileMime = $this->libFileMime->getMime(array_merge($userPath, $workPath, [$item]));
                $this->processed = true;
            }
        } catch (FilesException | FormsException | PathsException | MimeException $ex) {
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
