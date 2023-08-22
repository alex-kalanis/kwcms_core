<?php

namespace KWCMS\modules\Sysinfo\AdminControllers;


use kalanis\kw_auth_sources\Interfaces\IWorkClasses;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;


/**
 * Class Sysinfo
 * @package KWCMS\modules\Sysinfo\AdminControllers
 * System info - DO NOT ALLOW ACCESS OUTSIDE THE ADMIN!
 */
class Sysinfo extends AAuthModule implements IModuleTitle
{
    final public function allowedAccessClasses(): array
    {
        return [IWorkClasses::CLASS_MAINTAINER, IWorkClasses::CLASS_ADMIN, ];
    }

    final public function run(): void
    {
    }

    public function result(): Output\AOutput
    {
        $out = new Output\Html();
        ob_start();
        phpinfo();
        $content = ob_get_clean();
        return $out->setContent($this->insideStyles($content) . $this->insideBody($content));
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
