<?php

namespace KWCMS\modules\Sysinfo\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_modules\Output;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;


/**
 * Class Sysinfo
 * @package KWCMS\modules\Sysinfo\AdminControllers
 * System info - DO NOT ALLOW ACCESS OUTSIDE THE ADMIN!
 */
class Sysinfo extends AAuthModule implements IHasTitle
{
    protected $content = '';

    public function __construct(...$constructParams)
    {
    }

    final public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, ];
    }

    final public function run(): void
    {
        ob_start();
        phpinfo();
        $content = ob_get_clean();
        $this->content = $this->insideStyles($content) . $this->insideBody($content);
    }

    public function result(): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->content);
    }

    protected function insideBody(string $content): string
    {
        if (preg_match('#<body[^>]*>(.*)<\/body>#muis', $content, $matches)) {
            return $matches[1];
        } else {
            return $content;
        }
    }

    protected function insideStyles(string $content): string
    {
        if (preg_match('#(<style[^>]*>.*<\/style>)#muis', $content, $matches)) {
            return $matches[1];
        } else {
            return '';
        }
    }

    public function getTitle(): string
    {
        return 'System info';
    }
}
