<?php

namespace KWCMS\modules\MdTexts\AdminControllers;


use kalanis\kw_modules\Output;
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

    public function __construct()
    {
        parent::__construct();
        $this->libMarkdown = new MarkdownExtra();
    }

    protected function getParams(): Texts\Lib\Params
    {
        return new Lib\Params();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Raw();
        $page = new Texts\Lib\PreviewTemplate();
        $page->setData($this->error ? $this->error->getMessage() :
            $this->libMarkdown->transform($this->displayContent)
        );
        return $out->setContent($page->render());
    }
}
