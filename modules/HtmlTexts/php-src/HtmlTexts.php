<?php

namespace KWCMS\modules\HtmlTexts;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Texts\TextsException;


/**
 * Class HtmlTexts
 * @package KWCMS\modules\HtmlTexts
 * Html-texts - extra things
 */
class HtmlTexts extends AAuthModule
{
    protected $targetPath = '';

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $target = Stuff::pathToArray(Stored::getPath()->getPath());
        $type = reset($target);
        $page = end($target);
        if (empty($type) || empty($page)) {
            $this->error = new TextsException(Lang::get('Unknown page query'));
            return;
        }
        $path = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'style', $type, $page]));
        if (empty($path)) {
            $this->error = new TextsException(Lang::get('Unknown page target'));
            return;
        }
        if (!is_file($path)) {
            $this->error = new TextsException(Lang::get('Not a readable file'));
            return;
        }
        $this->targetPath = $path;
    }

    public function result(): Output\AOutput
    {
        $out = new Output\Raw();
        return $out->setContent(file_get_contents($this->targetPath));
    }
}
