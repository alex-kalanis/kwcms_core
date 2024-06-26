<?php

namespace KWCMS\modules\Images\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Access;
use kalanis\kw_files\Node;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_styles\Styles;
use kalanis\kw_table\core\TableException;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
use KWCMS\modules\Images\Lib;
use KWCMS\modules\Images\Templates;


/**
 * Class Dashboard
 * @package KWCMS\modules\Images\AdminControllers
 * Site's image content - list available files in current dir
 */
class Dashboard extends AAuthModule implements IHasTitle
{
    use Templates\TModuleTemplate;
    use TWhereDir;
    use Lib\TLibAction;

    protected UserDir $userDir;
    protected Access\CompositeAdapter $files;
    protected ITree $tree;
    /** @var string[] */
    protected array $availableTypes = ['bmp', 'gif', 'jpeg', 'jpg', 'pic', 'png', 'tif', 'tiff', 'wbmp', 'webp', ];
    /** @var string[] */
    protected array $userPath = [];
    /** @var string[] */
    protected array $currentPath = [];

    protected $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        $this->files = (new Access\Factory(new FilesTranslations()))->getClass($constructParams);
        $this->tree = new DataSources\Files($this->files);
        $this->userDir = new UserDir(new Lib\Translations());
        $this->initLibAction(new images_factory(
            $this->files,
            null,
            null,
            null,
            new ImagesTranslations()
        ));
        $this->constructParams = $constructParams;
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    /**
     * @throws FilesException
     */
    public function run(): void
    {
        // get list of files to display
        // depends on current dir and passed dir
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $this->userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $this->currentPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->tree->setStartPath(array_merge($this->userPath, $this->currentPath));
            $this->tree->wantDeep(false);
            $this->tree->setFilterCallback([$this, 'filterFiles']);
            $this->tree->process();

        } catch (PathsException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    public function filterFiles(Node $info): bool
    {
        if (empty($info->getPath())) {
            return true; // init node
        }
        $pt = array_values($info->getPath());
        $end = end($pt);
        if (empty($end)) {
            return false;
        }
        return $info->isFile() && in_array(strtolower(Stuff::fileExt(strval($end))), $this->availableTypes);
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        if (!empty($this->error)) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        }
        Styles::want('Images', 'dashboard.css');
        try {
            $table = new Lib\ListTable(
                $this->inputs,
                $this->links,
                $this->getLibFileAction($this->constructParams, $this->userPath, $this->currentPath),
                array_merge($this->userPath, $this->currentPath)
            );
            return $out->setContent($this->outModuleTemplate($table->getTable($this->tree)->render()));
        } catch ( ConnectException | FormsException | ImagesException | LangException | TableException $ex) {
            return $out->setContent($this->outModuleTemplate($ex->getMessage() . nl2br($ex->getTraceAsString())));
        }
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'files' => $this->tree->getRoot(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('images.page');
    }
}
