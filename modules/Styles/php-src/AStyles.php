<?php

namespace KWCMS\modules\Styles;


use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
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


/**
 * Class AStyles
 * @package KWCMS\modules\Styles\Controllers
 * Render styles in page
 */
abstract class AStyles extends AModule
{
    /** @var IMime */
    protected $mime = null;
    /** @var StylesTemplate */
    protected $template = null;
    /** @var ExternalLink */
    protected $libExtLink = '';
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var InnerLinks */
    protected $innerLink = null;
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
            boolval(Config::get('Core', 'site.more_lang', false))
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
        $this->treeList = new Files($this->files);
        $this->mime = (new Check\Factory())->getLibrary(null);
    }

    public function process(): void
    {
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
        $extPath = $this->innerLink->toUserPath(
            $this->arrPath->setString(
                $this->getFromParam('path', '')
            )->getArray()
        );
        $content = [];
        if ($this->files->isDir($extPath)) {
            foreach ($this->filesInPath($extPath) as $style) {
                $content[] = $this->template->reset()->setData(
                    $this->libExtLink->linkVariant(
                        $this->arrPath->setArray($style->getPath())->getString(),
                        'styles',
                        true
                    )
                )->render();
            }
        }
        foreach (ExStyles::getAll() as $module => $scripts) {
            foreach ($scripts as $script) {
                $content[] = $this->template->reset()->setData(
                    $this->libExtLink->linkVariant($module . '/' . $script, 'styles', true, false)
                )->render();
            }
        }
        $out = new Output\Html();
        return $out->setContent(implode('', $content));
    }

    /**
     * @throws FilesException
     * @throws MimeException
     * @throws PathsException
     * @throws StylesException
     * @return Output\AOutput
     */
    abstract protected function outContent(): Output\AOutput;

    /**
     * @param string[] $extPath
     * @throws FilesException
     * @throws PathsException
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

    public function filterCss(Node $file): bool
    {
        return $file->isFile()
            && in_array(Stuff::fileExt(
                $this->arrPath->setArray($file->getPath())->getFileName()
            ), ['css']);
    }
}
