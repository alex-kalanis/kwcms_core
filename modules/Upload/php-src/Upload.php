<?php

namespace KWCMS\modules\Upload;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use kalanis\kw_tree\TWhereDir;
use kalanis\UploadPerPartes\Response;
use KWCMS\modules\Files\Lib as FileLib;


/**
 * Class Upload
 * @package KWCMS\modules\Sysinfo
 * Upload files
 */
class Upload extends AAuthModule implements IModuleTitle
{
    use FileLib\TLibAction;
    use Lib\TModuleTemplate;
    use TWhereDir;

    protected $inSteps = '';
    /** @var Lib\Uploader */
    protected $lib = null;

    public function __construct()
    {
        $this->initTModuleTemplate();
        Config::load('Upload');
        $this->lib = new Lib\Uploader();
    }

    final public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $pathArray = Stuff::pathToArray(Stored::getPath()->getPath());
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

    protected function stepInit(): Response\AResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        $userDir = $this->getUserDirLib();
        return $this->lib->init(
            $userDir->getWebRootDir() . $userDir->getHomeDir() . $this->getWhereDir() . DIRECTORY_SEPARATOR,
            strval($inputs['fileName']),
            strval($inputs['fileSize'])
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

    protected function stepDone(): Response\AResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        $result = $this->lib->done(
            strval($inputs['sharedKey'])
        );
        /** @var Response\DoneResponse $result */

        $act = $this->getLibAction();
        $act->renameFile(
            Stuff::filename($result->getTemporaryLocation()),
            Stuff::filename($act->findFreeName($result->getFileName()))
        );

        return $result;
    }

    public function getTitle(): string
    {
        return Lang::get('upload.page');
    }
}
