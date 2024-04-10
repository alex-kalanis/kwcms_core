<?php

namespace KWCMS\modules\MdTexts\Controllers;


use kalanis\kw_modules\Mixer\PassedParams\SingleParam;
use kalanis\kw_modules\Output;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\MdTexts\Lib;
use Michelf\MarkdownExtra\MarkdownExtra;


/**
 * Class Preview
 * @package KWCMS\modules\MdTexts
 * Site's text preview - show what will be rendered and saved
 */
class Sub extends AModule
{
    use Lib\TModuleTemplate;

    protected MarkdownExtra $libMarkdown;
    protected array $passedParams = [];

    public function __construct(...$constructParams)
    {
        $this->libMarkdown = new MarkdownExtra();
    }

    public function process(): void
    {
        // nothing here
    }

    public function passParamsAs(): string
    {
        return SingleParam::class;
    }

    public function output(): Output\AOutput
    {
        $firstParam = isset($this->params[0]) ? strval($this->params[0]) : '';
        return (new Output\Raw())
            ->setContent(
                $this->libMarkdown->transform(
                    $firstParam
                )
            );
    }
}
