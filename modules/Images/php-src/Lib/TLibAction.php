<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Access\Factory;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Traits\TLang;
use KWCMS\modules\Images\Interfaces;


/**
 * Trait TLibAction
 * @package KWCMS\modules\Images\Lib
 * How process actions over content
 */
trait TLibAction
{
    use TLang;

    private ?Factory $factory;

    protected function initLibAction(Factory $factory): void
    {
        $this->factory = $factory;
        $this->setImLang($factory->getImLang());
    }

    /**
     * @throws ImagesException
     * @return Factory
     */
    protected function getLibImageFactory(): Factory
    {
        if (empty($this->factory)) {
            throw new ImagesException($this->getImLang()->imSizesNotSet());
        }
        return $this->factory;
    }

    /**
     * @param mixed $filesParams
     * @param string[] $userPath
     * @param string[] $currentPath
     * @throws ImagesException
     * @return Interfaces\IProcessFiles
     */
    protected function getLibFileAction($filesParams, array $userPath, array $currentPath): Interfaces\IProcessFiles
    {
        return new ProcessFile(
            $this->getLibImageFactory()->getOperations($filesParams),
            $this->getLibImageFactory()->getUpload($filesParams),
            $this->getLibImageFactory()->getImages($filesParams),
            $userPath,
            $currentPath
        );
    }

    /**
     * @param mixed $filesParams
     * @param string[] $userPath
     * @param string[] $currentPath
     * @throws ImagesException
     * @return Interfaces\IProcessDirs
     */
    protected function getLibDirAction($filesParams, array $userPath, array $currentPath): Interfaces\IProcessDirs
    {
        return new ProcessDir(
            $this->getLibImageFactory()->getDirs($filesParams),
            $userPath,
            $currentPath
        );
    }
}
