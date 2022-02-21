<?php

namespace kalanis\kw_menu;


use kalanis\kw_menu\Interfaces\IMenu;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_paths\Stuff;


/**
 * Class MoreFiles
 * @package kalanis\kw_menu
 * Process the menu against the file tree
 * Load more already unloaded files and remove non-existing ones
 */
class MoreFiles
{
    /** @var string */
    protected $metaFile = '';
    /** @var string */
    protected $directoryPath = '';
    /** @var DataProcessor */
    protected $data = null;
    /** @var Interfaces\IDataSource */
    protected $storage = null;

    public function __construct(Interfaces\IDataSource $storage, string $metaFile = '', ?IMNTranslations $lang = null)
    {
        $this->data = new DataProcessor($storage, $lang);
        $this->storage = $storage;
        $this->metaFile = !empty($metaFile) ? Stuff::filename($metaFile) : 'index' . IMenu::EXT_MENU ;
    }

    /**
     * @param string $directoryPath directory
     * @return $this
     */
    public function setPath(string $directoryPath): self
    {
        $this->directoryPath = Stuff::removeEndingSlash($directoryPath);
        $this->data->setPath($this->directoryPath . DIRECTORY_SEPARATOR . $this->metaFile);
        return $this;
    }

    /**
     * @return $this
     * @throws MenuException
     */
    public function load(): self
    {
        if ($this->data->exists()) {
            $this->data->load();
            $this->fillMissing();
        } else {
            $this->createNew();
        }
        return $this;
    }

    /**
     * @throws MenuException
     */
    protected function createNew(): void
    {
        foreach ($this->storage->getFiles($this->directoryPath) as $file) {
            $this->data->add($file);
        }
    }

    /**
     * @throws MenuException
     */
    protected function fillMissing(): void
    {
        $toRemoval = array_map([$this, 'fileName'], $this->data->getWorking());
        $toRemoval = array_combine($toRemoval, array_fill(0, count($toRemoval), true));

        foreach ($this->storage->getFiles($this->directoryPath) as $file) {
            $alreadyKnown = false;
            foreach ($this->data->getWorking() as $item) { # stay
                if ((!$alreadyKnown) && ($item->getFile() == $file)) {
                    $alreadyKnown = true;
                    $toRemoval[$item->getFile()] = false;
                }
            }
            if (!$alreadyKnown) {
                $this->data->add($file);
            }
        }
        foreach ($this->data->getWorking() as $item) {
            if (!empty($toRemoval[$item->getFile()])) {
                $this->data->remove($item->getFile());
            }
        }
    }

    public function fileName(Menu\Item $item): string
    {
        return $item->getFile();
    }

    public function getData(): DataProcessor
    {
        return $this->data;
    }
}
