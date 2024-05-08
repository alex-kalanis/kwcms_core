<?php

namespace KWCMS\modules\Scripts\AdminControllers;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Support;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_scripts\Scripts as ExScripts;
use kalanis\kw_scripts\ScriptsException;
use KWCMS\modules\Scripts\AScripts;


/**
 * Class Scripts
 * @package KWCMS\modules\Scripts\AdminControllers
 * Render scripts in admin page
 */
class Scripts extends AScripts
{
    /**
     * @throws MimeException
     * @throws PathsException
     * @throws ScriptsException
     * @return Output\AOutput
     */
    public function outContent(): Output\AOutput
    {
        $gotPath = $this->getFromInput('path', [], [IEntry::SOURCE_EXTERNAL]);
        $gotPath = is_object($gotPath) ? $gotPath->getValue() : $gotPath;
        $modulePath = is_array($gotPath) ? $gotPath : (new ArrayPath())->setString(strval($gotPath))->getArray();
        $moduleName = array_shift($modulePath);
        $moduleName = Support::normalizeModuleName($moduleName);
        $content = ExScripts::getFile($moduleName, Stuff::arrayToPath($modulePath));
        if ($content) {
            header('Content-Type: ' . $this->mime->getMime(['any.js']));
        }
        $out = new Output\Raw();
        $out->setContent($content);
        return $out;
    }
}
