<?php

namespace KWCMS\modules\Images;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stored;
use kalanis\kw_styles\Styles;
use kalanis\kw_table\core\TableException;
use kalanis\kw_tree\Tree;
use kalanis\kw_tree\TWhereDir;
use SplFileInfo;


/**
 * Class Dashboard
 * @package KWCMS\modules\Images
 * Site's image content - list available files in current dir
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Templates\TModuleTemplate;
    use TWhereDir;

    /** @var UserDir|null */
    protected $userDir = null;
    /** @var Tree|null */
    protected $tree = null;
    protected $availableTypes = ['bmp', 'gif', 'jpeg', 'jpg', 'pic', 'png', 'tif', 'tiff', 'wbmp', 'webp', ];

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->tree = new Tree(Stored::getPath());
        $this->userDir = new UserDir(Stored::getPath());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        // get list of files to display
        // depends on current dir and passed dir
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());
        $this->userDir->process();
        $this->tree->canRecursive(false);
        $this->tree->startFromPath($this->userDir->getHomeDir() . $this->getWhereDir());
        $this->tree->setFilterCallback([$this, 'filterFiles']);
        $this->tree->process();
    }

    public function filterFiles(SplFileInfo $info): bool
    {
        return $info->isFile() && in_array($info->getExtension(), $this->availableTypes);
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
                FilesHelper::get($this->userDir->getWebRootDir() . $this->userDir->getWorkDir()),
                $this->getWhereDir()
            );
            return $out->setContent($this->outModuleTemplate($table->getTable($this->tree)->render()));
        } catch ( FormsException | TableException | ConnectException | ImagesException $ex) {
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
                'files' => $this->tree->getTree(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('images.page');
    }
}
