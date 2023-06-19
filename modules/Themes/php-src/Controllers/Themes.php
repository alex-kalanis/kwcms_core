<?php

namespace KWCMS\modules\Themes\Controllers;


use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_styles\Styles as ExStyles;
use kalanis\kw_styles\StylesException;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Themes\StylesTemplate;


/**
 * Class Themes
 * @package KWCMS\modules\Themes\Controllers
 * Render Themes in page
 */
class Themes extends AModule
{
    use TToString;

    /** @var IMime */
    protected $mime = null;
    /** @var StylesTemplate */
    protected $template = null;
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var InnerLinks */
    protected $innerLink = null;
    /** @var ExternalLink */
    protected $libExtLink = '';
    /** @var string[] */
    protected $extPath = [];
    /** @var string[] */
    protected $dirPath = [];
    /** @var Files */
    protected $treeList = null;

    /**
     * @throws FilesException
     * @throws PathsException
     */
    public function __construct()
    {
        $this->template = new StylesTemplate();
        $this->libExtLink = new ExternalLink(Stored::getPath(), StoreRouted::getPath(), false, false);
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            false
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
        $this->treeList = new Files($this->files);
        $this->mime = (new Check\Factory())->getLibrary(null);
    }

    /**
     * @throws PathsException
     */
    public function process(): void
    {
        $extPath = Stuff::linkToArray(strval($this->getFromParam('target')));
        $this->extPath = $extPath;
        $this->dirPath = $this->innerLink->toUserPath($extPath);
    }

    /**
     * @throws FilesException
     * @throws MimeException
     * @throws PathsException
     * @throws StylesException
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_LAYOUT) ? $this->outLayout() : $this->outContent();
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return Output\AOutput
     */
    public function outLayout(): Output\AOutput
    {
        $content = [];
        $add = 0;
        $titleName = $this->getFromParam('titleName', 'skin');

        if ($this->files->exists($this->dirPath)) {
            foreach ($this->filesInPath($this->dirPath) as $style) {
                $content[] = $this->template->reset()->setData(
                    $this->libExtLink->linkVariant(
                        $this->arrPath->setArray($style->getPath())->getString(),
                        'styles',
                        true
                    ),
                    strtr($style, '/', ''),
                    sprintf('%s%d', $titleName, $add)
                )->render();

                $add++;
            }
        }
        foreach (ExStyles::getAll() as $module => $styles) {
            foreach ($styles as $style) {
                $content[] = $this->template->reset()->setData(
                    $this->libExtLink->linkVariant($module . '/' . $style, 'styles', true, false),
                    strtr($style, '/', ''),
                    sprintf('%s%d', $titleName, $add)
                )->render();

                $add++;
            }
        }
        $out = new Output\Html();
        return $out->setContent(implode('', $content));
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @throws MimeException
     * @throws StylesException
     * @return Output\AOutput
     */
    public function outContent(): Output\AOutput
    {
        $gotPath = array_values($this->extPath);
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
     * @param string[] $extPath
     * @throws PathsException
     * @throws FilesException
     * @return FileNode[]
     */
    protected function filesInPath(array $extPath): array
    {
        $this->treeList->setStartPath($extPath);
        $this->treeList->wantDeep(false);
        $this->treeList->setFilterCallback([$this, 'filterCss']);
        $this->treeList->process();
        $data = $this->treeList->getRoot();
        return $data ? $data->getSubNodes() : [];
    }

    /**
     * @param Node $file
     * @return bool
     */
    public function filterCss(Node $file): bool
    {
        return $file->isFile()
            && in_array(Stuff::fileExt(
                $this->arrPath->setArray($file->getPath())->getFileName()
            ), ['css']);
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
