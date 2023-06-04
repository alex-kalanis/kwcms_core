<?php

namespace KWCMS\modules\Watermark\Libs;


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
 * @package KWCMS\modules\Watermark\Libs
 */
class ImageFill
{
    use TLang;
    use TType;

    /** @var Sources\Image */
    protected $libImage = null;
    /** @var ImageProcessor */
    protected $processor1 = null;
    /** @var ImageProcessor */
    protected $processor2 = null;
    /** @var ISizes */
    protected $config = null;

    public function __construct(ImageProcessor $processor, ISizes $config, Sources\Image $image, ?IIMTranslations $lang = null)
    {
        $this->setImLang($lang);
        $this->libImage = $image;
        $this->processor1 = clone $processor;
        $this->processor2 = clone $processor;
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
     * @param string[] $sourcePath
     * @param string[]|string $watermarkPath
     * @param bool $repeat
     * @throws FilesException
     * @throws ImagesException
     * @throws MimeException
     * @throws PathsException
     */
    public function process(array $sourcePath, $watermarkPath, bool $repeat): void
    {
        // get image from the storage
        $tempSourcePath = strval(tempnam(sys_get_temp_dir(), $this->config->getTempPrefix()));
        $resSource = $this->libImage->get($sourcePath);
        if (empty($resSource)) {
            throw new FilesException($this->getImLang()->imThumbCannotGetBaseImage());
        }

        if (false === @file_put_contents($tempSourcePath, $resSource)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getImLang()->imThumbCannotStoreTemporaryImage());
        }
        // @codeCoverageIgnoreEnd

        $this->processor1->load($this->getType($sourcePath), $tempSourcePath);

        // get watermark from the storage
        if (is_array($watermarkPath)) {
            $tempMarkPath = strval(tempnam(sys_get_temp_dir(), $this->config->getTempPrefix()));
            $resMark = $this->libImage->get($watermarkPath);
            if (empty($resMark)) {
                throw new FilesException($this->getImLang()->imThumbCannotGetBaseImage());
            }

            if (false === @file_put_contents($tempMarkPath, $resMark)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getImLang()->imThumbCannotStoreTemporaryImage());
            }
            // @codeCoverageIgnoreEnd

            $this->processor2->load($this->getType($watermarkPath), $tempMarkPath);
        } else {
            $this->processor2->load($this->getType($watermarkPath), $watermarkPath);
        }


        $imX = $this->processor1->width();
        $imY = $this->processor1->height();
        $wmX = $this->processor2->width();
        $wmY = $this->processor2->height();

        imagecopy($this->processor1->getResource(), $this->processor2->getResource(), ($imX/2)-($wmX/2), ($imY/2)-($wmY/2), 0, 0, $wmX, $wmY);

        if ($repeat) {
            $waterless = $imX - $wmX;
            $rest = ceil($waterless/($wmX/2));

            for ($n=1; $n<=$rest; $n++) {
                imagecopy($this->processor1->getResource(), $this->processor2->getResource(), (($imX/2)-($wmX/2))-($wmX*$n), ($imY/2)-($wmY/2), 0, 0, $wmX, $wmY);
                imagecopy($this->processor1->getResource(), $this->processor2->getResource(), (($imX/2)-($wmX/2))+($wmX*$n), ($imY/2)-($wmY/2), 0, 0, $wmX, $wmY);
            }
        }
    }

    /**
     * @param string[] $sourcePath
     * @throws ImagesException
     * @throws MimeException
     */
    public function render(array $sourcePath): void
    {
        $this->processor1->render($this->getType($sourcePath));
    }
}
