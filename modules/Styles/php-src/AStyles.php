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
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_styles\Styles as ExStyles;
use kalanis\kw_styles\StylesException;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Core\Libs\FilesTranslations;


/**
 * Class AStyles
 * @package KWCMS\modules\Styles\Controllers
 * Render styles in page
 */
abstract class AStyles extends AModule
{
    protected IMime $mime;
    protected StylesTemplate $template;
    protected ExternalLink $libExtLink;
    protected ArrayPath $arrPath;
    protected CompositeAdapter $files;
    protected InnerLinks $innerLink;
    protected Files $treeList;

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->template = new StylesTemplate();
        $this->libExtLink = new ExternalLink(StoreRouted::getPath(), false, false);
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'site.more_lang', false)),
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
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
        $pt = $this->getFromParam('path', []);
        $extPath = $this->innerLink->toUserPath(
            $this->arrPath->setArray(
                is_array($pt) ? $pt : Stuff::linkToArray(strval($pt))
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
                    $this->libExtLink->linkVariant($module . '/' . $script, 'styles', true)
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
