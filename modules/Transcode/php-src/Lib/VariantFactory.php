<?php

namespace KWCMS\modules\Transcode\Lib;


class VariantFactory
{
    protected $path = '';
    protected $available = [];

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

    public function getVariant(string $name): ?AVariant
    {
        $this->check($name);
        $className = __NAMESPACE__ . '\Variants\\' . $name;
        return new $className();
    }
}
