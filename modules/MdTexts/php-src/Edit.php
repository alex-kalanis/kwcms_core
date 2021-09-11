<?php

namespace KWCMS\modules\MdTexts;


use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\StorageException;
use KWCMS\modules\Texts;
use Michelf\MarkdownExtra\MarkdownExtra;


/**
 * Class Edit
 * @package KWCMS\modules\MdTexts
 * Site's text content - edit correct file
 */
class Edit extends Texts\Edit
{
    use Lib\TModuleTemplate;

    protected $libMarkDown = null;

    public function __construct()
    {
        parent::__construct();
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
        if (!in_array($ext, $this->getParams()->filteredTypes())) {
            $this->error = new Texts\TextsException(Lang::get('texts.file_wrong_type'));
            return;
        }
        $path = Stuff::sanitize($this->userDir->getHomeDir() . $this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName);
        try {
            $content = $this->storage->exists($path) ? $this->storage->get($path) : '{CREATE_NEW_FREE_FILE}';
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
                $this->isProcessed = $this->storage->set($path, $content) && $this->storeMdAsHtml($path, $content);
            }
        } catch (Texts\TextsException | StorageException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    /**
     * @param string $path
     * @param string $content
     * @return bool
     * @throws StorageException
     */
    protected function storeMdAsHtml(string $path, string $content): bool
    {
        $partName = Stuff::fileBase($path); // Must stay as full path
        $origExt = Stuff::fileExt(Stuff::filename($partName));
        if (empty($origExt) || !in_array($origExt, ['htm', 'html', 'xhtm', 'xhtml'])) {
            // no html, no need to save another data
            return true;
        }
        // it's html content! Save it too!
        return $this->storage->set($partName, $this->libMarkDown->transform($content));
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
