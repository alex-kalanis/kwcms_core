<?php

namespace KWCMS\modules\Styles\Controllers;


use kalanis\kw_confs\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Support;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_styles\Styles as ExStyles;
use KWCMS\modules\Styles\AStyles;


/**
 * Class Styles
 * @package KWCMS\modules\Styles\Controllers
 * Render styles in page
 */
class Styles extends AStyles
{
    use TToString;

    public function outContent(): Output\AOutput
    {
        $gotPath = $this->params['path']->getValue();
        $content = $this->getUserContent($gotPath);
        if (is_null($content)) {
            $moduleName = array_shift($gotPath);
            $moduleName = Support::normalizeModuleName($moduleName);
            $content = ExStyles::getFile($moduleName, Stuff::arrayToPath($gotPath));
        }
        if ($content) {
            header('Content-Type: ' . $this->mime->getMime(['any.css']));
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
