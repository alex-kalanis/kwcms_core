<?php

namespace KWCMS\modules\Short\AdminControllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Short\Lib;


/**
 * Class ChDir
 * @package KWCMS\modules\Short\AdminControllers
 * Change directory - by short messages
 */
class ChDir extends \KWCMS\modules\Admin\AdminControllers\ChDir implements IHasTitle
{
    use Lib\TModuleTemplate;

    /**
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->initTModuleTemplate();
        Config::load('Short');
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->outModuleTemplate($content));
    }

    public function getTitle(): string
    {
        return Lang::get('short.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}
