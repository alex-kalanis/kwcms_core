<?php

namespace KWCMS\modules\Images\AdminControllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Images\Templates;


/**
 * Class ChDir
 * @package KWCMS\modules\Images\AdminControllers
 * Change directory - by Images
 */
class ChDir extends \KWCMS\modules\Admin\AdminControllers\ChDir implements IHasTitle
{
    use Templates\TModuleTemplate;

    /**
     * ChDir constructor.
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->initTModuleTemplate();
        Config::load('Images');
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->outModuleTemplate($content));
    }

    public function getTitle(): string
    {
        return Lang::get('images.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}
