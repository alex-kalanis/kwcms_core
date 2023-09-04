<?php

namespace KWCMS\modules\MdTexts\AdminControllers;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Texts;
use KWCMS\modules\MdTexts\Lib;
use Michelf\MarkdownExtra\MarkdownExtra;


/**
 * Class Edit
 * @package KWCMS\modules\MdTexts\AdminControllers
 * Site's text content - edit correct file
 */
class Edit extends Texts\AdminControllers\Edit
{
    use Lib\TModuleTemplate;

    protected $libMarkDown = null;

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->libMarkDown = new MarkdownExtra();
    }

    public function run(): void
    {
        $this->runTTexts($this->inputs, $this->user->getDir());
        $fileName = $this->getFromParam('fileName');
        if (empty($fileName)) {
            $this->error = new Texts\TextsException(Lang::get('texts.file_not_sent'));
            return;
        }
        $ext = Stuff::fileExt(Stuff::filename($fileName));
        if (!in_array($ext, $this->getParams()->whichExtsIWant())) {
            $this->error = new Texts\TextsException(Lang::get('texts.file_wrong_type'));
            return;
        }

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $fullPath = array_merge($userPath, Stuff::linkToArray($this->getWhereDir()), [strval($fileName)]);

            $content = $this->files->isFile($fullPath) ? $this->files->readFile($fullPath) : '{CREATE_NEW_FREE_FILE}';
            $this->editFileForm->composeForm($content, $fileName, $this->links->linkVariant($this->targetPreview()));
            $this->editFileForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->editFileForm->process()) {
                $content = strval($this->editFileForm->getValue('content'));
                if (empty($content)) {
                    $content = base64_decode($this->editFileForm->getValue('content_base64'), true);
                    if (false === $content) {
                        throw new Texts\TextsException(Lang::get('texts.file_wrong_content'));
                    }
                }
                $this->isProcessed = $this->files->saveFile($fullPath, $content) && $this->storeMdAsHtml($fullPath, $content);
            }
        } catch (Texts\TextsException | FilesException | PathsException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    /**
     * @param string[] $path
     * @param string $content
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    protected function storeMdAsHtml(array $path, string $content): bool
    {
        $libArr = new ArrayPath();
        $libArr->setArray($path);
        $partName = Stuff::fileBase($libArr->getFileName()); // Must stay as file name
        $origExt = Stuff::fileExt($partName); // just ext to compare
        if (empty($origExt) || !in_array($origExt, ['htm', 'html', 'xhtm', 'xhtml'])) {
            // no html, no need to save another data
            return true;
        }
        // it's html content! Save it too!
        return $this->files->saveFile(array_merge(
            $libArr->getArrayDirectory(),
            [$partName]
        ), $this->libMarkDown->transform($content));
    }

    protected function getParams(): Texts\Lib\Params
    {
        return new Lib\Params();
    }

    protected function targetPreview(): string
    {
        return 'md-texts/preview';
    }

    protected function targetDone(): string
    {
        return 'md-texts/dashboard';
    }
}
