<?php

namespace KWCMS\modules\Styles\AdminControllers;


use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_styles\Styles as ExStyles;
use kalanis\kw_styles\StylesException;
use KWCMS\modules\Styles\AStyles;


/**
 * Class Styles
 * @package KWCMS\modules\Styles\AdminControllers
 * Render styles in admin page
 */
class Styles extends AStyles
{
    /**
     * @throws PathsException
     * @throws MimeException
     * @throws StylesException
     * @return Output\AOutput
     */
    public function outContent(): Output\AOutput
    {
        $modulePath = Stuff::linkToArray($this->params['path']);
        $moduleName = array_shift($modulePath);
        $moduleName = Support::normalizeModuleName($moduleName);
        $content = ExStyles::getFile($moduleName, Stuff::arrayToPath($modulePath));
        if ($content) {
            header('Content-Type: ' . $this->mime->mimeByPath('any.css'));
        }
        $out = new Output\Raw();
        $out->setContent($content);
        return $out;
    }
}
