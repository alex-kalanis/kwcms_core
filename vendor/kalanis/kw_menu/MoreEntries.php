<?php

namespace kalanis\kw_menu;


use kalanis\kw_paths\PathsException;


/**
 * Class MoreEntries
 * @package kalanis\kw_menu
 * Process the menu against the file tree
 * Load more already unloaded entries and remove non-existing ones
 */
class MoreEntries
{
    /** @var string[] */
    protected $groupKey = [];
    /** @var MetaProcessor */
    protected $meta = null;
    /** @var Interfaces\IEntriesSource */
    protected $dataSource = null;

    public function __construct(MetaProcessor $metaSource, Interfaces\IEntriesSource $dataSource)
    {
        $this->meta = $metaSource;
        $this->dataSource = $dataSource;
    }

    /**
     * @param string[] $groupKey directory to scan
     * @return $this
     */
    public function setGroupKey(array $groupKey): self
    {
        $this->groupKey = $groupKey;
        return $this;
    }

    /**
     * @param string[] $metaKey file/id with meta data
     * @throws MenuException
     * @return $this
     */
    public function setMeta(array $metaKey): self
    {
        $this->meta->setKey($metaKey);
        return $this;
    }

    /**
     * @throws MenuException
     * @throws PathsException
     * @return $this
     */
    public function load(): self
    {
        if ($this->meta->exists()) {
            $this->meta->load();
            $this->fillMissing();
        } else {
            $this->createNew();
        }
        return $this;
    }

    /**
     * @throws MenuException
     * @throws PathsException
     */
    protected function createNew(): void
    {
        foreach ($this->dataSource->getFiles($this->groupKey) as $file) {
            $this->meta->addEntry($file);
        }
    }

    /**
     * @throws MenuException
     * @throws PathsException
     */
    protected function fillMissing(): void
    {
        $toRemoval = array_map([$this, 'entryId'], $this->meta->getWorking());
        $toRemoval = (array) array_combine($toRemoval, array_fill(0, count($toRemoval), true));

        foreach ($this->dataSource->getFiles($this->groupKey) as $file) {
            $alreadyKnown = false;
            foreach ($this->meta->getWorking() as $item) { # stay
                if ((!$alreadyKnown) && ($item->getId() == $file)) {
                    $alreadyKnown = true;
                    $toRemoval[$item->getId()] = false;
                }
            }
            if (!$alreadyKnown) {
                $this->meta->addEntry($file);
            }
        }
        foreach ($this->meta->getWorking() as $item) {
            if (!empty($toRemoval[$item->getId()])) {
                $this->meta->removeEntry($item->getId());
            }
        }
    }

    public function entryId(Menu\Entry $item): string
    {
        return $item->getId();
    }

    public function getMeta(): MetaProcessor
    {
        return $this->meta;
    }
}
