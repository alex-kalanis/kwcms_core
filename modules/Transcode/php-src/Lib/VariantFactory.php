<?php

namespace KWCMS\modules\Transcode\Lib;


use kalanis\kw_forms\Exceptions\FormsException;


class VariantFactory
{
    /** @var string */
    protected $path = '';
    /** @var string[] */
    protected $available = [];

    public function __construct(?string $path = null)
    {
        $this->path = $this->setPath($path);
        $this->available = $this->loadNames();
    }

    protected function setPath(?string $path): string
    {
        $realPath = empty($path) ? implode(DIRECTORY_SEPARATOR, ['', 'Variants'])
            : (is_array($path) ? implode(DIRECTORY_SEPARATOR, $path) : $path);
        return __DIR__ . $realPath;
    }

    /**
     * @return string[]
     */
    protected function loadNames(): array
    {
        $names = [];
        foreach (new \FilesystemIterator($this->path) as $splObject) {
            if ($splObject->isFile()) {
                $names[] = $splObject->getBasename('.php');
            }
        }
        return $names;
    }

    /**
     * @return string[]
     */
    public function getList(): array
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

    /**
     * @param string $name
     * @throws FormsException
     * @return AVariant|null
     */
    public function getVariant(string $name): ?AVariant
    {
        $this->check($name);
        $className = __NAMESPACE__ . '\Variants\\' . $name;

        try {
            /** @var class-string $className */
            $ref = new \ReflectionClass($className);
            $class = $ref->newInstance();
            if ($class instanceof AVariant) {
                return $class;
            }
            return null;
        } catch (\ReflectionException $ex) {
            throw new FormsException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
