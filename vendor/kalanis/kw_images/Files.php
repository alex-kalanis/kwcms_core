<?php

namespace kalanis\kw_images;


use kalanis\kw_extras\ExtrasException;


/**
 * Class Files
 * Operations over files
 * @package kalanis\kw_images
 */
class Files
{
    protected $libImage = null;
    protected $libThumb = null;
    protected $libDesc = null;

    public function __construct(Files\Image $image, Files\Thumb $thumb, Files\Desc $desc)
    {
        $this->libImage = $image;
        $this->libThumb = $thumb;
        $this->libDesc = $desc;
    }

    /**
     * @param string $path
     * @param string $desc
     * @param bool $hasThumb
     * @return bool
     * @throws ImagesException
     */
    public function add(string $path, string $desc = '', bool $hasThumb = true): bool
    {
        $this->libImage->check($path);

        $this->libThumb->delete($path);
        if ($hasThumb) {
            $this->libThumb->create($path);
        }

        if (!empty($desc)) {
            $this->libDesc->set($path, $desc);
        } else {
            $this->libDesc->delete($path);
        }

        return true;
    }

    /**
     * @param string $path
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function copy(string $path, string $targetDir, bool $overwrite = false): bool
    {
        $this->libImage->copy($path, $targetDir, $overwrite);
        $this->libThumb->copy($path, $targetDir, $overwrite);
        $this->libDesc->copy($path, $targetDir, $overwrite);
        return true;
    }

    /**
     * @param string $path
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function move(string $path, string $targetDir, bool $overwrite = false): bool
    {
        $this->libImage->move($path, $targetDir, $overwrite);
        $this->libThumb->move($path, $targetDir, $overwrite);
        $this->libDesc->move($path, $targetDir, $overwrite);
        return true;
    }

    /**
     * @param string $path
     * @param string $targetName
     * @param bool $overwrite
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function rename(string $path, string $targetName, bool $overwrite = false): bool
    {
        $this->libImage->rename($path, $targetName, $overwrite);
        $this->libThumb->rename($path, $targetName, $overwrite);
        $this->libDesc->rename($path, $targetName, $overwrite);
        return true;
    }

    /**
     * @param string $path
     * @return bool
     * @throws ImagesException
     */
    public function delete(string $path): bool
    {
        $this->libDesc->delete($path);
        $this->libThumb->delete($path);
        $this->libImage->delete($path);
        return true;
    }
}
