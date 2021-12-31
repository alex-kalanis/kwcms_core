<?php

namespace KWCMS\modules\Transcode\Lib;


class Selector
{
    protected $path = '';
    protected $available = [];
    /** @var AVariant */
    protected $used = null;

    public function __construct(?string $path = null)
    {
        $this->path = $this->setPath($path);
        $this->available = $this->loadNames();
    }

    protected function setPath(?string $path)
    {
        $realPath = empty($path) ? implode(DIRECTORY_SEPARATOR, ['', 'Variants'])
            : (is_array($path) ? implode(DIRECTORY_SEPARATOR, $path) : $path);
        return __DIR__ . $realPath;
    }

    protected function loadNames()
    {
        $names = [];
        foreach (new \FilesystemIterator($this->path) as $splObject) {
            if ($splObject->isFile()) {
                $names[] = $splObject->getBasename('.php');
            }
        }
        return $names;
    }

    public function getList()
    {
        return $this->available;
    }

    public function isAvailable(string $fileName): bool
    {
        return in_array($fileName, $this->available);
    }

    public function check(string $fileName): void
    {
        if (!$this->isAvailable($fileName)) {
            throw new \LogicError('Unknown transcode');
        }
    }

    public function useMode(string $name): void
    {
        $this->check($name);
        $className = __NAMESPACE__ . '\Variants\\' . $name;
        $this->used = new $className();
    }

    public function getFrom()
    {
        return $this->used->getFrom();
    }

    public function getTo()
    {
        return $this->used->getTo();
    }

    public function getSeparator()
    {
        return $this->used->getSeparator();
    }

    public function getAllowed()
    {
        return $this->used->getAllowed();
    }

    public function getSpecials()
    {
        return $this->used->specials();
    }

    public function getLeftoversFrom()
    {
        return $this->used->leftOversFrom();
    }

    public function getLeftoversTo()
    {
        return $this->used->leftOversTo();
    }
}