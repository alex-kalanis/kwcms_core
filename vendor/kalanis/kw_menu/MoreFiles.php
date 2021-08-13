<?php

namespace kalanis\kw_menu;


use kalanis\kw_menu\Interfaces\IMenu;
use kalanis\kw_paths\Stuff;


/**
 * Class MoreFiles
 * @package kalanis\kw_menu
 * Load more already unloaded files and remove non-existing ones
 */
class MoreFiles
{
    protected $path = '';
    /** @var DataProcessor */
    protected $data = null;

    public function __construct()
    {
        $this->data = new DataProcessor();
    }

    /**
     * @param string $path directory
     * @return DataProcessor
     * @throws MenuException
     */
    public function setPath(string $path): DataProcessor
    {
        $this->path = $path;
        $filePath = $path . DIRECTORY_SEPARATOR . $this->menuFileName();
        $this->data->setPath($filePath);
        if (is_file($filePath)) { # meta already exists
            $this->data->load();
            $this->fillMissing();
        } else {
            $this->createNew();
        }
        return $this->data;
    }

    public function createNew(): void
    {
        foreach ($this->listFiles() as $file) {
            $this->data->add($file);
        }
    }

    protected function fillMissing(): void
    {
        $listed = $this->listFiles();
        $toRemoval = [];
        foreach ($this->data->getWorking() as $item) {
            $toRemoval[$item->getPosition()] = true;
        }
        foreach ($listed as $fileName) {
            $alreadyKnown = false;
            foreach ($this->data->getWorking() as $item) { # stay
                if ((!$alreadyKnown) && ($item->getFile() == $fileName)) {
                    $alreadyKnown = true;
                    $toRemoval[$item->getPosition()] = false;
                }
            }
            if (!$alreadyKnown) {
                $this->data->add($fileName);
            }
        }
        foreach ($this->data->getWorking() as $item) {
            if (!empty($toRemoval[$item->getPosition()])) {
                $this->data->remove($item->getFile());
            }
        }
    }

    public function listFiles(): array
    {
        return array_filter(array_filter(scandir($this->path), ['\kalanis\kw_paths\Stuff', 'notDots']), [$this, 'onlyHtml']);
    }

    public function onlyHtml(string $in): bool
    {
        return in_array(Stuff::fileExt($in), ['htm', 'html']);
    }

    protected function menuFileName(): string
    {
        return 'index' . IMenu::EXT_MENU;
    }
}
