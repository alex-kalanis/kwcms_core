<?php

namespace KWCMS\modules\Dirlist\Controllers;


use kalanis\kw_address_handler\Sources\Inputs;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_pager\BasicPager;
use kalanis\kw_paging\Positions;
use kalanis\kw_paging\Render\SimplifiedPager;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_user_paths\InnerLinks;
use kalanis\kw_user_paths\UserInnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
use KWCMS\modules\Dirlist\Libs;
use KWCMS\modules\Dirlist\Templates;


/**
 * Class Dirlist
 * @package KWCMS\modules\Dirlist\Controllers
 * Listing the directory
 * @link http://page.kwcms_core.lemp.test/
 */
class Dirlist extends AModule
{
    protected string $module = '';
    protected Templates\Main $templateMain;
    protected Templates\Row $templateRow;
    protected Templates\Display $templateDisplay;
    protected ExternalLink $linkExternal;
    protected Images $libImages;
    /** @var SimplifiedPager|null */
    protected $pager = null;
    protected ArrayPath $arrPath;
    protected CompositeAdapter $files;
    protected UserInnerLinks $innerLink;
    protected Files $treeList;
    /** @var string[] */
    protected array $path = [];
    /** @var string[] */
    protected array $dir = [];
    protected string $preselectExt = '';

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws PathsException
     * @throws ConfException
     * @throws ImagesException
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        $this->defineModule();
        Lang::load($this->module);
        Config::load($this->module);
        $this->templateMain = new Templates\Main();
        $this->templateRow = new Templates\Row();
        $this->templateDisplay = new Templates\Display();
        $this->linkExternal = new ExternalLink(StoreRouted::getPath());
        $this->files = (new files_factory(new FilesTranslations()))->getClass($constructParams);
        $this->libImages = (new images_factory(
            $this->files,
            null,
            null,
            null,
            new ImagesTranslations()
        ))->getImages($constructParams);
        $this->arrPath = new ArrayPath();
        $this->innerLink = new UserInnerLinks(
            strval(
                Config::get('Core', 'page.default_user', isset($constructParams['default_user'])
                    ? $constructParams['default_user']
                    : '/user/'
                )
            )
        );
        $this->treeList = new Files($this->files);
    }

    protected function defineModule(): void
    {
        $this->module = static::getClassName(static::class);
    }

    /**
     * @throws PathsException
     * @throws FilesException
     */
    public function process(): void
    {
        $this->path = $this->pathLookup();
        $this->dir = $this->innerLink->toFullPath($this->path);
//print_r(['dirlist pt' => $this->path, 'dr' => $this->dir]);
        $this->pager = new SimplifiedPager(new Positions(new BasicPager()), new Libs\Linking(new Inputs($this->inputs)));

        if ($this->files->isDir($this->dir)) {
            $this->preselectExt = $this->getFromParam('ext', '');
            $this->treeList
                ->wantDeep(false)
                ->setStartPath($this->dir)
                ->setFilterCallback([$this, 'isUsable'])
                ->setOrdering(
                    'desc' == $this->getFromParam('order', '') ? ITree::ORDER_DESC : ITree::ORDER_ASC
                )
                ->process();
            if ($this->treeList->getRoot() && $this->treeList->getRoot()->getSubNodes()) {
                $this->pager->getPager()
                    ->setActualPage($this->actualPageLookup())
                    ->setMaxResults(count($this->treeList->getRoot()->getSubNodes()));
            }
        }
    }

    /**
     * @throws PathsException
     * @return string[]
     */
    protected function pathLookup(): array
    {
        if (empty($this->params['target'])) {
            $this->arrPath->setArray(StoreRouted::getPath()->getPath());
            return array_merge($this->arrPath->getArrayDirectory(), [Stuff::fileBase($this->arrPath->getFileName())]);
        }
        $preset = strval($this->params['target']);
        return (IPaths::SPLITTER_SLASH != $preset[0])
            ? array_merge(StoreRouted::getPath()->getPath(), Stuff::linkToArray(Stuff::fileBase($preset))) // add current path to wanted content
            : Stuff::linkToArray(Stuff::fileBase(mb_substr($preset, 1))); // just remove that slash and return path
    }

    protected function actualPageLookup(): int
    {
        $actualPages = $this->inputs->getInArray(Libs\Linking::PAGE_KEY, [
            IEntry::SOURCE_CLI, IEntry::SOURCE_POST, IEntry::SOURCE_GET
        ]);
        return !empty($actualPages) ? intval(strval(reset($actualPages))) : Positions::FIRST_PAGE ;
    }

    public function isUsable(Node $file): bool
    {
        $this->arrPath->setArray($file->getPath());
        if (!$file->isFile()) {
            return false;
        }

        if ('.' == $this->arrPath->getFileName()[0]) {
            return false;
        }

        // compare test only for lower suffixes
        $ext = strtolower(Stuff::fileExt($this->arrPath->getFileName()));
        if (!empty($this->preselectExt) && ($ext != $this->preselectExt)) {
            return false;
        }

        $denyTypes = Config::get($this->module, 'deny_types', []);
        foreach ($denyTypes as $denyType) {
            if ($ext == strtolower($denyType)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return AOutput
     */
    public function output(): AOutput
    {
        $renderStyle = strval($this->getFromParam('style', Config::get($this->module, 'style')));
        $rows = intval($this->getFromParam('row', Config::get($this->module, 'rows')));
        $columns = intval($this->getFromParam('col', Config::get($this->module, 'cols')));
        $directPaging = intval($this->getFromParam('paging', Config::get($this->module, 'direct_paging', true)));

        $this->templateDisplay->setTemplateName($renderStyle);
        // re-count cols
        if (!empty($this->templateDisplay->getStyles()[$renderStyle])) {
            $rows = $columns * $rows;
            $columns = 1;
        }

        $filesChunks = $this->getFilesChunked($rows, $columns);
        $out = new Html();

        if (empty($filesChunks)) {
            return $out;
        }

        $lines = [];
        foreach ($filesChunks as $files) {
            $cols = [];
            foreach ($files as $file) {
                $fullPath = array_merge($this->path, $file->getPath());
                $ext = strtolower(Stuff::fileExt($this->arrPath->setArray($file->getPath())->getFileName()));
                $cols[] = $this->templateDisplay->reset()->setData(
                    $this->getIcon($ext),
                    $this->getLink($fullPath, $ext),
                    $this->getThumb($fullPath, $ext),
                    $this->getName($file),
                    $this->getDetails($fullPath, $renderStyle),
                    $this->getInfo($file)
                )->render();
            }
            $lines[] = $this->templateRow->reset()->setData(implode('', $cols))->render();
        }

        $this->templateMain->reset()->setData(
            implode('', $lines),
            $this->pager,
            $directPaging
        );
        return $out->setContent($this->templateMain->render());
    }

    /**
     * @param int $chunkPos
     * @param int $filesPerChunk
     * @return FileNode[][]
     */
    protected function getFilesChunked(int $chunkPos, int $filesPerChunk): array
    {
        $this->pager->getPager()->setLimit(intval($filesPerChunk * $chunkPos));
        if ($this->treeList->getRoot() && $this->treeList->getRoot()->getSubNodes()) {
            return array_chunk(
                array_slice(
                    $this->treeList->getRoot()->getSubNodes(),
                    $this->pager->getPager()->getOffset(),
                    $this->pager->getPager()->getLimit()
                ),
                $filesPerChunk
            );
        } else {
            // no nodes, no files
            return [];
        }
    }

    /**
     * @param string[] $path
     * @param string $ext
     * @throws PathsException
     * @return string
     */
    protected function getLink(array $path, string $ext): string
    {
        if (in_array($ext, ['htm', 'html', 'xhtml', 'xhtm', 'htx', 'htt', 'php', ])) { // create links for normally accessible pages
            return $this->linkExternal->linkVariant(Stuff::arrayToLink($path));
        } else { // rest of the world must pass as file passed through external link
            return $this->linkExternal->linkVariant(Stuff::arrayToLink($path), 'file', true);
        }
    }

    /**
     * @param string[] $path
     * @param string $ext
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    protected function getThumb(array $path, string $ext): string
    {
        $pt = $this->libImages->reverseThumbPath($path);
        return $this->files->isFile($this->innerLink->toFullPath($pt))
            ? $this->linkExternal->linkVariant(Stuff::arrayToLink($pt), 'Image', true)
            : $this->getIcon($ext)
        ;
    }

    /**
     * @param string $ext
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    protected function getIcon(string $ext): string
    {
        return $this->files->isFile($this->innerLink->toModulePath('Sysimage', ['images', 'files', $ext.'.png']))
            ? $this->linkExternal->linkVariant('files/'.$ext.'.png', 'sysimage', true)
            : $this->linkExternal->linkVariant('files/dummy.png', 'sysimage', true);
    }

    protected function getName(FileNode $file): string
    {
        return $this->arrPath->setArray($file->getPath())->getFileName();
    }

    /**
     * @param string[] $path
     * @param string $renderStyle
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    protected function getDetails(array $path, string $renderStyle): string
    {
        $detailContent = $this->libImages->getDescription($this->innerLink->toFullPath($path));
        if (empty($detailContent)) {
            return '';
        }

        $descMaxLen = intval(Config::get($this->module, 'desc_maxlen', 1000));
        if ((strlen($detailContent) > $descMaxLen) && !empty($this->templateDisplay->getStyles()[$renderStyle])) {
            $detailContent = substr($detailContent, 0, ($descMaxLen + 5));
            $detailContent = substr($detailContent, 0, strrpos($detailContent,' '));
            $detailContent .= '...';
        };
        return $detailContent;
    }

    protected function getInfo(FileNode $file): string
    {
        return '';
    }
}
