<?php

namespace KWCMS\modules\Texts\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\AAuthModule;
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

    /** @var IMime */
    protected $mime = null;
    /** @var string[] */
    protected $fullPath = [];
    /** @var string */
    protected $displayContent = '';

    /**
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate(StoreRouted::getPath());
        $this->initTTexts(Stored::getPath());
        $this->mime = (new Check\Factory())->getLibrary($this->files);
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
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
        $ext = Stuff::fileExt(Stuff::filename($fileName));
        if (!in_array($ext, $this->getParams()->whichExtsIWant())) {
            $this->error = new TextsException(Lang::get('texts.file_wrong_type'));
            return;
        }

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $this->fullPath = array_merge($userPath, Stuff::linkToArray($this->getWhereDir()), [strval($fileName)]);

            $externalContent = $this->inputs->getInArray('content', [IEntry::SOURCE_POST, IEntry::SOURCE_GET, IEntry::SOURCE_CLI]);
            $this->displayContent = (!empty($externalContent))
                ? strval(reset($externalContent))
                : ( $this->files->isFile($this->fullPath)
                    ? $this->files->readFile($this->fullPath)
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

    /**
     * @throws MimeException
     * @return Output\AOutput
     */
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

    /**
     * @throws MimeException
     * @return Output\AOutput
     */
    public function outRaw(): Output\AOutput
    {
        header('Content-Type: ' . $this->mime->getMime($this->fullPath));
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

    /**
     * @throws MimeException
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
                'form_result' => 0,
                'form_errors' => [],
                'content_type' => $this->mime->getMime($this->fullPath),
                'content_base64' => base64_encode($this->displayContent),
            ]);
            return $out;
        }
    }
}
