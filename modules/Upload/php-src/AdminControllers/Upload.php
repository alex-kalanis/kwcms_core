<?php

namespace KWCMS\modules\Upload\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use kalanis\UploadPerPartes\Target;
use kalanis\UploadPerPartes\Responses;
use kalanis\UploadPerPartes\Target\Local\DrivingFile;
use kalanis\UploadPerPartes\Target\Local\TemporaryStorage;
use kalanis\UploadPerPartes\Uploader;
use kalanis\UploadPerPartes\UploadException;
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

    protected string $inSteps = '';
    /** @var Uploader $lib */
    protected Uploader $lib;
    protected UserDir $userDir;
    protected FileLib\Processor $processor;
    protected Access\CompositeAdapter $files;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        Config::load('Upload');
        $this->files = (new Access\Factory(new FilesTranslations()))->getClass($constructParams);
        $lang = new Lib\Translations();
        $this->processor = new FileLib\Processor($this->files);
        $this->userDir = new UserDir($lang);
    }

    final public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    /**
     * @throws PathsException
     * @throws UploadException
     */
    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $pathArray = StoreRouted::getPath()->getPath();
        if ('steps' == reset($pathArray)) {
            $this->inSteps = next($pathArray);
        }
        $this->userDir->setUserPath($this->user->getDir());
        $this->userDir->process();
        $lang = new Lib\Translations();
        $this->lib = new Uploader(null, [
            'lang' => $lang,
            'temp_location' => $this->userDir->getFullPath()->getString() . DIRECTORY_SEPARATOR . 'upload',
            'target_location' => $this->userDir->getFullPath()->getString(), // target location - path
            'target' => $this->userDir->getFullPath()->getString(), // if local or remote
            'driving_file' => $this->files,
            'data_encoder' => DrivingFile\DataEncoders\Text::class,
            'data_modifier' => DrivingFile\DataModifiers\Clear::class,
            'key_encoder' => DrivingFile\KeyEncoders\Name::class,
            'key_modifier' => DrivingFile\KeyModifiers\Suffix::class,
            'temp_storage' => $this->files,
            'temp_encoder' => TemporaryStorage\KeyEncoders\Name::class,
            'final_storage' => $this->files,
            'final_encoder' =>  new Lib\FinalEncoder(null, $lang),
            'checksum' => Target\Local\Checksums\Factory::FORMAT_MD5, // how to check parts
            'decoder' => Target\Local\ContentDecoders\Factory::FORMAT_BASE64, // how to pass data in parts
        ]);
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
        Scripts::want('upload', 'CheckSumMD5GregHolt.js');
        Scripts::want('upload', 'EncoderBase64.js');
        Scripts::want('upload', 'EncoderHex2.js');
        Scripts::want('upload', 'uploader.js');
        Scripts::want('upload', 'into_page.js');
        Styles::want('upload', 'upload.css');
        Styles::want('upload', 'into_page.css');
        return $out->setContent($this->outModuleTemplate($tmpl->render()));
    }

    protected function steps(): Output\AOutput
    {
        $out = new Output\Json();
        $methodToUse = 'step'. ucfirst(strtolower($this->inSteps));
        if (method_exists($this, $methodToUse)) {
            $out->setContent((array) $this->$methodToUse());
        } else {
            $out->setContent(['ERROR', 'Unknown action!']);
        }
        return $out;
    }

    /**
     * @throws FormsException
     * @return Responses\BasicResponse
     */
    protected function stepInit(): Responses\BasicResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        $this->userDir->setUserPath($this->user->getDir());
        return $this->lib->init( // change from array-based paths to string-based ones
            $this->getWhereDir(),
            strval($inputs['fileName']),
            intval(strval($inputs['fileSize']))
        );
    }

    protected function stepCheck(): Responses\BasicResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->check(
            strval($inputs['serverData']),
            intval(strval($inputs['segment'])),
            strval($inputs['method'])
        );
    }

    protected function stepTrim(): Responses\BasicResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->truncateFrom(
            strval($inputs['serverData']),
            intval(strval($inputs['segment']))
        );
    }

    protected function stepFile(): Responses\BasicResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->upload(
            strval($inputs['serverData']),
            strval($inputs['content']),
            strval($inputs['method'])
        );
    }

    protected function stepCancel(): Responses\BasicResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->cancel(
            strval($inputs['serverData'])
        );
    }

    /**
     * @return Responses\BasicResponse
     */
    protected function stepDone(): Responses\BasicResponse
    {
        $inputs = $this->inputs->getInArray(null, [IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        return $this->lib->done(
            strval($inputs['serverData'])
        );
    }

    public function getTitle(): string
    {
        return Lang::get('upload.page');
    }
}
