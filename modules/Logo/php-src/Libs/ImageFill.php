<?php

namespace KWCMS\modules\Logo\Libs;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Interfaces\ISizes;
use kalanis\kw_images\Sources;
use kalanis\kw_images\Traits\TLang;
use kalanis\kw_images\Traits\TType;
use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\PathsException;


/**
 * Class ImageFill
 * Fill image from source
 * @package KWCMS\modules\Logo\Libs
 */
class ImageFill
{
    use TLang;
    use TType;

    /** @var Sources\Image */
    protected $libImage = null;
    /** @var ImageProcessor */
    protected $processor = null;
    /** @var ISizes */
    protected $config = null;

    public function __construct(ImageProcessor $processor, ISizes $config, Sources\Image $image, ?IIMTranslations $lang = null)
    {
        $this->setImLang($lang);
        $this->libImage = $image;
        $this->processor = $processor;
        $this->config = $config;
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function exists(array $path): bool
    {
        return $this->libImage->isHere($path);
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return int
     */
    public function created(array $path): int
    {
        return intval($this->libImage->getCreated($path));
    }

    /**
     * @param string[]|string $logoPath
     * @throws FilesException
     * @throws ImagesException
     * @throws MimeException
     * @throws PathsException
     */
    public function process($logoPath): void
    {
        // get logo from the storage
        if (is_array($logoPath)) {
            $tempMarkPath = strval(tempnam(sys_get_temp_dir(), $this->config->getTempPrefix()));
            $resMark = $this->libImage->get($logoPath);
            if (empty($resMark)) {
                throw new FilesException($this->getImLang()->imThumbCannotGetBaseImage());
            }

            if (false === @file_put_contents($tempMarkPath, $resMark)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getImLang()->imThumbCannotStoreTemporaryImage());
            }
            // @codeCoverageIgnoreEnd

            $this->processor->load($this->getType($logoPath), $tempMarkPath);
        } else {
            $this->processor->load($this->getType($logoPath), $logoPath);
        }
    }

    /**
     * @param string[] $sourcePath
     * @throws ImagesException
     * @throws MimeException
     */
    public function render(array $sourcePath): void
    {
        $this->processor->render($this->getType($sourcePath));
    }

    /**
     * @throws ImagesException
     */
    public function close(): void
    {
        imagedestroy($this->processor->getResource());
    }
}
