<?php

namespace KWCMS\modules\Styles\AdminControllers;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Support;
use kalanis\kw_paths\ArrayPath;
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
        $gotPath = $this->getFromInput('path', [], [IEntry::SOURCE_EXTERNAL]);
        $gotPath = is_object($gotPath) ? $gotPath->getValue() : $gotPath;
        $modulePath = is_array($gotPath) ? $gotPath : (new ArrayPath())->setString(strval($gotPath))->getArray();
        $moduleName = array_shift($modulePath);
        $moduleName = Support::normalizeModuleName($moduleName);
        $content = ExStyles::getFile($moduleName, Stuff::arrayToPath($modulePath));
        if ($content) {
            header('Content-Type: ' . $this->mime->getMime(['any.css']));
        }
        $out = new Output\Raw();
        $out->setContent($content);
        return $out;
    }
}
