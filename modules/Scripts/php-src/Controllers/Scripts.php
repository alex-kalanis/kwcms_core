<?php

namespace KWCMS\modules\Scripts\Controllers;


use kalanis\kw_confs\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_scripts\Scripts as ExScripts;
use kalanis\kw_scripts\ScriptsException;
use KWCMS\modules\Scripts\AScripts;


/**
 * Class Scripts
 * @package KWCMS\modules\Scripts\Controllers
 * Render scripts in page
 */
class Scripts extends AScripts
{
    use TToString;

    /**
     * @throws FilesException
     * @throws MimeException
     * @throws PathsException
     * @throws ScriptsException
     * @return Output\AOutput
     */
    protected function outContent(): Output\AOutput
    {
        $gotPath = Stuff::linkToArray($this->params['path']);
        $content = $this->getUserContent($gotPath);
        if (is_null($content)) {
            $moduleName = array_shift($gotPath);
            $moduleName = Support::normalizeModuleName($moduleName);
            $content = ExScripts::getFile($moduleName, Stuff::arrayToPath($gotPath));
        }
        if ($content) {
            header('Content-Type: ' . $this->mime->getMime(['any.js']));
        }
        $out = new Output\Raw();
        $out->setContent($content);
        return $out;
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return string|null
     */
    protected function getUserContent(array $path): ?string
    {
        $userPath = $this->innerLink->toUserPath([]);
        $confStyle = Config::get('Core', 'page.default_style', 'default');
        $localPath = array_merge($userPath, [IPaths::DIR_STYLE, $confStyle], $path);
        if ($this->files->isFile($localPath)) {
            return $this->toString(Stuff::arrayToPath($localPath), $this->files->readFile($localPath));
        }

        $localPath = array_merge($userPath, [IPaths::DIR_STYLE], $path);
        if ($this->files->isFile($localPath)) {
            return $this->toString(Stuff::arrayToPath($localPath), $this->files->readFile($localPath));
        }

        return null;
    }
}
