<?php

namespace KWCMS\modules\Texts\AdminControllers;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_storage\StorageException;
use KWCMS\modules\Texts\Lib;
use KWCMS\modules\Texts\TextsException;


/**
 * Class Preview
 * @package KWCMS\modules\Texts\AdminControllers
 * Site's text preview - show what will be rendered and saved
 */
class Preview extends AAuthModule
{
    use Lib\TTexts;
    use Lib\TModuleTemplate;

    /** @var MimeType|null */
    protected $mime = null;
    /** @var string */
    protected $ext = '';
    /** @var string */
    protected $displayContent = '';

    public function __construct()
    {
        $this->initTModuleTemplate(Stored::getPath(), StoreRouted::getPath());
        $this->initTTexts(Stored::getPath());
        $this->mime = new MimeType(true);
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->runTTexts($this->inputs, $this->user->getDir());

        $fileName = $this->inputs->getInArray('fileName', [IEntry::SOURCE_POST, IEntry::SOURCE_GET, IEntry::SOURCE_CLI]);
        if (empty($fileName)) {
            $this->error = new TextsException(Lang::get('texts.file_not_sent'));
            return;
        }
        $fileName = reset($fileName);
        $this->ext = Stuff::fileExt(Stuff::filename($fileName));
        if (!in_array($this->ext, $this->getParams()->filteredTypes())) {
            $this->error = new TextsException(Lang::get('texts.file_wrong_type'));
            return;
        }

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $fullPath = array_merge($userPath, Stuff::linkToArray($this->getWhereDir()), [strval($fileName)]);

            $externalContent = $this->inputs->getInArray('content', [IEntry::SOURCE_POST, IEntry::SOURCE_GET, IEntry::SOURCE_CLI]);
            $this->displayContent = (!empty($externalContent))
                ? strval(reset($externalContent))
                : ( $this->files->isFile($fullPath)
                    ? $this->files->readFile($fullPath)
                    : ''
                );
        } catch (FilesException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getParams(): Lib\Params
    {
        return new Lib\Params();
    }

    public function result(): Output\AOutput
    {
        return $this->isRaw()
            ? $this->outRaw()
            : (
                $this->isJson()
                ? $this->outJson()
                : $this->outHtml()
            )
        ;
    }

    public function outRaw(): Output\AOutput
    {
        header('Content-Type: ' . $this->mime->mimeByExt($this->ext));
        $out = new Output\Raw();
        return $out->setContent($this->displayContent);
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Raw();
        $page = new Lib\PreviewTemplate();
        $page->setData($this->error ? $this->error->getMessage() : $this->displayContent);
        return $out->setContent($page->render());
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'form_result' => 0,
                'form_errors' => [],
                'content_type' => $this->mime->mimeByExt($this->ext),
                'content_base64' => base64_encode($this->displayContent),
            ]);
            return $out;
        }
    }
}
