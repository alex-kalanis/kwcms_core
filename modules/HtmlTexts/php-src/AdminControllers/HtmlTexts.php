<?php

namespace KWCMS\modules\HtmlTexts\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Texts\TextsException;


/**
 * Class HtmlTexts
 * @package KWCMS\modules\HtmlTexts\AdminControllers
 * Html-texts - extra things
 * For popup windows in html editor
 */
class HtmlTexts extends AAuthModule
{
    /** @var string */
    protected $targetPath = '';

    public function __construct(...$constructParams)
    {
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $target = StoreRouted::getPath()->getPath();
        $type = strval(reset($target));
        $page = strval(end($target));
        if (empty($type) || empty($page)) {
            $this->error = new TextsException(Lang::get('Unknown page query'));
            return;
        }
        $path = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'style', $type, $page]));
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
        if ($this->error) {
            return $out->setContent($this->error->getMessage());
        }
        return $out->setContent(file_get_contents($this->targetPath));
    }
}
