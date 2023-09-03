<?php

namespace KWCMS\modules\Upload\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use kalanis\UploadPerPartes\Exceptions\UploadException;
use kalanis\UploadPerPartes\Response;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Files\Lib as FileLib;
use KWCMS\modules\Upload\Lib;
use KWCMS\modules\Upload\UploadTemplate;


/**
 * Class Upload
 * @package KWCMS\modules\Upload\AdminControllers
 * Upload files
 */
class Upload extends AAuthModule implements IHasTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;

    protected $inSteps = '';
    /** @var Lib\Uploader */
    protected $lib = null;
    /** @var UserDir */
    protected $userDir = null;
    /** @var FileLib\Processor */
    protected $processor = null;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     * @throws UploadException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        Config::load('Upload');
        $files = (new Access\Factory(new FilesTranslations()))->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
        $lang = new Lib\Translations();
        $this->processor = new FileLib\Processor($files);
        $this->userDir = new UserDir($lang);
        $this->lib = new Lib\Uploader($files);
    }

    final public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $pathArray = StoreRouted::getPath()->getPath();
        if ('steps' == reset($pathArray)) {
            $this->inSteps = next($pathArray);
        }
    }

    protected function getUserDir(): string
    {
        return $this->user->getDir();
    }

    public function result(): Output\AOutput
    {
        return $this->inSteps ? $this->steps() : $this->resultDefault();
    }

    public function resultDefault(): Output\AOutput
    {
        $out = new Output\Html();
        $tmpl = new UploadTemplate();
        $tmpl->setData(
            $this->links->linkVariant('steps/init', 'upload', true),
            $this->links->linkVariant('steps/check', 'upload', true),
            $this->links->linkVariant('steps/cancel', 'upload', true),
            $this->links->linkVariant('steps/trim', 'upload', true),
            $this->links->linkVariant('steps/file', 'upload', true),
            $this->links->linkVariant('steps/done', 'upload', true)
        );
        Scripts::want('upload', 'md5GregHolt.js');
        Scripts::want('upload', 'uploader.js');
        Styles::want('upload', 'upload.css');
        return $out->setContent($this->outModuleTemplate($tmpl->render()));
    }

    protected function steps(): Output\AOutput
    {
        $out = new Output\Json();
        $methodToUse = 'step'. ucfirst(strtolower($this->inSteps));
        if (method_exists($this, $methodToUse)) {
            $out->setContent($this->$methodToUse());
        } else {
            $out->setContent(['ERROR', 'Unknown action!']);
        }
        return $out;
    }

    /**
     * @throws PathsException
     * @return Response\AResponse
     */
    protected function stepInit(): Response\AResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        $this->userDir->setUserPath($this->user->getDir());
        return $this->lib->init( // change from array-based paths to string-based ones
            Stuff::arrayToPath(array_merge(
                $this->userDir->process()->getFullPath()->getArray(),
                Stuff::linkToArray($this->getWhereDir()),
                [] // empty for adding ending separator
            )),
            strval($inputs['fileName']),
            intval(strval($inputs['fileSize']))
        );
    }

    protected function stepCheck(): Response\AResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->check(
            strval($inputs['sharedKey']),
            intval(strval($inputs['segment']))
        );
    }

    protected function stepCancel(): Response\AResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->cancel(
            strval($inputs['sharedKey'])
        );
    }

    protected function stepTrim(): Response\AResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->truncateFrom(
            strval($inputs['sharedKey']),
            intval(strval($inputs['segment']))
        );
    }

    protected function stepFile(): Response\AResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->upload(
            strval($inputs['sharedKey']),
            base64_decode(strval($inputs['content']))
        );
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @throws UploadException
     * @return Response\AResponse
     */
    protected function stepDone(): Response\AResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        $result = $this->lib->done(
            strval($inputs['sharedKey'])
        );
        /** @var Response\DoneResponse $result */

        $this->userDir->setUserPath($this->getUserDir());

        $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
        $workPath = Stuff::linkToArray($this->getWhereDir());

        $this->processor->setUserPath($userPath)->setWorkPath($workPath);
        $this->processor->renameFile(
            Stuff::filename($result->getTemporaryLocation()),
            Stuff::filename($result->getFileName())
        );

        return $result;
    }

    public function getTitle(): string
    {
        return Lang::get('upload.page');
    }
}
