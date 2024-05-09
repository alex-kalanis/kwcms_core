<?php

namespace KWCMS\modules\Texts\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Access\Factory as modules_factory;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Mixer\Processor;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\RoutedPath;
use kalanis\kw_routed_paths\Sources\Arrays;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
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

    protected IMime $mime;
    /** @var string[] */
    protected array $userPath = [];
    /** @var string[] */
    protected array $fullPath = [];
    protected string $displayContent = '';
    protected Processor $subModules;
    protected InnerLinks $innerLink;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected array $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws LangException
     * @throws ModuleException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate(StoreRouted::getPath());
        $this->initTTexts($constructParams);

        $this->constructParams = $constructParams;
        // this part is about module loader, it depends one each server
        $modulesFactory = new modules_factory();
        $loader = $modulesFactory->getLoader(['modules_loaders' => [$constructParams, 'web']]);
        $moduleProcessor = $modulesFactory->getModulesList($constructParams);
        $moduleProcessor->setModuleLevel(ISitePart::SITE_CONTENT);
        $this->subModules = new Processor($loader, $moduleProcessor);

        // paths from render
        $this->innerLink = new InnerLinks(
            new RoutedPath(new Arrays([])),
            boolval(Config::get('Core', 'site.more_users', false)),
            false,
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );

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
            $this->userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $this->fullPath = array_merge($this->userPath, Stuff::linkToArray($this->getWhereDir()), [strval($fileName)]);

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
     * @throws PathsException
     * @throws ModuleException
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

    /**
     * @throws ModuleException
     * @throws PathsException
     * @return Output\AOutput
     */
    public function outHtml(): Output\AOutput
    {
        $this->params['target'] = $this->localizedUserPath();
        $out = new Output\Raw();
        $page = new Lib\PreviewTemplate();
        $page->setData(
            $this->error
                ? $this->error->getMessage()
                : $this->subModules->fill(
                    $this->displayContent,
                    $this->inputs,
                    ISitePart::SITE_CONTENT,
                    $this->params,
                    $this->constructParams
            )
        );
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

    /**
     * @throws PathsException
     * @return string
     */
    protected function localizedUserPath(): string
    {
        $this->userDir->setUserPath($this->userDir->getUserPath());
        $this->userDir->wantDataDir(true)->process();

//print_r(['up' => $this->userPath, 'fp' => $this->fullPath, 'gd' => $this->user->getDir(), 'uf' => $this->userDir->getFullPath()]);

        $ap = new ArrayPath();
        // remote first dirs
        $currentPath = array_values($this->userDir->getFullPath()->getArray());
        $fullPath = array_values($this->fullPath);
        foreach ($currentPath as $dir) {
            $first = array_shift($fullPath);
            if ($first != $dir) {
                array_unshift($fullPath, $first);
                break;
            }
        }
        return IPaths::SPLITTER_SLASH . $ap->setArray($fullPath)->getString();
    }
}
