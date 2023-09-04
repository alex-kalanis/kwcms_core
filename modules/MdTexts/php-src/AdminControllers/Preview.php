<?php

namespace KWCMS\modules\MdTexts\AdminControllers;


use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Texts;
use KWCMS\modules\MdTexts\Lib;
use Michelf\MarkdownExtra\MarkdownExtra;


/**
 * Class Preview
 * @package KWCMS\modules\MdTexts
 * Site's text preview - show what will be rendered and saved
 */
class Preview extends Texts\AdminControllers\Preview
{
    use Lib\TModuleTemplate;

    /** @var MarkdownExtra|null */
    protected $libMarkdown = null;

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->libMarkdown = new MarkdownExtra();
    }

    protected function getParams(): Texts\Lib\Params
    {
        return new Lib\Params();
    }

    public function outHtml(): Output\AOutput
    {
        $this->params['target'] = $this->localizedUserPath();
        $out = new Output\Raw();
        $page = new Texts\Lib\PreviewTemplate();
        $page->setData($this->error
            ? $this->error->getMessage()
            : $this->subModules->fill(
                $this->libMarkdown->transform($this->displayContent),
                $this->inputs,
                ISitePart::SITE_CONTENT,
                $this->params,
                $this->constructParams
            )
        );
        return $out->setContent($page->render());
    }

    protected function localizedUserPath(): string
    {
        return Stuff::fileBase(parent::localizedUserPath());
    }
}
