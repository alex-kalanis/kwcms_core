<?php

namespace clipr;


use kalanis\kw_clipr\Clipr\Useful;
use kalanis\kw_clipr\CliprException;
use kalanis\kw_clipr\Interfaces;
use kalanis\kw_clipr\Output\TPrettyTable;
use kalanis\kw_clipr\Tasks\ATask;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;


/**
 * Class Lister
 * @package clipr
 * @property string $path
 */
class Lister extends ATask
{
    use TPrettyTable;

    protected ArrayPath $arrPt;

    public function __construct()
    {
        $this->arrPt = new ArrayPath();
    }

    protected function startup(): void
    {
        parent::startup();
        $this->params->addParam('path', 'path', null, null, null, 'Specify own path to tasks');
    }

    public function desc(): string
    {
        return 'Render list of tasks available in paths defined for lookup';
    }

    public function process(): int
    {
        $this->writeLn('<yellow><bluebg>+====================================+</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>|              kw_clipr              |</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>+====================================+</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>| List all tasks available by lookup |</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>+====================================+</bluebg></yellow>');

        if (empty($this->loader)) {
            $this->sendErrorMessage('Need any loader to get tasks!');
            return static::STATUS_LIB_ERROR;
        }

        $this->setTableHeaders(['Task name', 'Call target', 'Description']);
        $this->setTableColors(['lgreen', 'lcyan', '']);

        try {
            $paths = $this->getPathsFromSubLoaders($this->loader);

            if ($this->path) {
                if (false === $real = realpath($this->path)) { // from relative to absolute path
                    throw new CliprException(sprintf('<redbg> !!! </redbg> Path leads to something unreadable. Path: <yellow>%s</yellow>', $this->path), static::STATUS_BAD_CONFIG);
                }
                $this->createOutput($paths, $this->arrPt->setString($real)->getArray());
            } else {
                foreach ($paths as $namespace => $path) {
                    if (false !== $real = realpath(DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path))) { // from relative to absolute path
                        $this->createOutput($paths, $this->arrPt->setString($real)->getArray());
                    }
                }
            }
        } catch (CliprException | PathsException $ex) {
            $this->writeLn($ex->getMessage());
            return $ex->getCode() ?: static::STATUS_ERROR;
        }
        $this->dumpTable();
        return static::STATUS_SUCCESS;
    }

    /**
     * Paths known by internal loaders
     * @param Interfaces\ILoader $loader
     * @throws CliprException
     * @return array<string, array<string>>
     */
    protected function getPathsFromSubLoaders(Interfaces\ILoader $loader): array
    {
        $paths = [];
        if ($loader instanceof Interfaces\ISubLoaders) {
            foreach ($loader->getLoaders() as $single) {
                $paths = array_merge($paths, $this->getPathsFromSubLoaders($single));
            }
        }
        if ($loader instanceof Interfaces\ITargetDirs) {
            $paths = array_merge($paths, $loader->getPaths());
        }
        return $paths;
    }

    /**
     * @param array<string, array<string>> $pathsForNamespaces
     * @param string[] $currentPath
     * @param bool $skipEmpty
     * @throws CliprException
     * @throws PathsException
     */
    protected function createOutput(array $pathsForNamespaces, array $currentPath, bool $skipEmpty = false): void
    {
        $known = realpath($full = DIRECTORY_SEPARATOR . $this->arrPt->setArray($currentPath)->getString());
        if (!is_dir($known)) {
            throw new CliprException(sprintf('<redbg> !!! </redbg> Path leads to something other than directory. Path: <yellow>%s</yellow>', $known), static::STATUS_BAD_CONFIG);
        }

        $allFiles = array_diff((array) scandir($known), [false, '', '.', '..']);
        $files = array_filter($allFiles, [$this, 'onlyPhp']);
        if (empty($files) && !$skipEmpty) {
            throw new CliprException(sprintf('<redbg> !!! </redbg> No usable files returned. Path: <yellow>%s</yellow>', $known), static::STATUS_NO_TARGET_RESOURCE);
        }

        foreach ($files as $fileName) {
            $className = $this->realFileToClass($pathsForNamespaces, $currentPath, $fileName);
            if ($className) {
                try {
                    $task = $this->loader ? $this->loader->getTask($className) : null;
                } catch (CliprException $ex) {
                    $task = null;
                }
                if (!$task) {
                    continue;
                }
                $task->initTask($this->translator, $this->inputs, $this->loader);
                $this->setTableDataLine([$className, Useful::getTaskCall($task), $task->desc()]);
            }
        }
        foreach ($allFiles as $fileName) {
            if (is_dir($known . DIRECTORY_SEPARATOR . $fileName)) {
                $this->createOutput($pathsForNamespaces, array_merge($currentPath, [$fileName]), true);
            }
        }
    }

    /**
     * @param array<string, array<string>> $availablePaths
     * @param string[] $dir
     * @param string $file
     * @throws PathsException
     * @return string|null
     */
    protected function realFileToClass(array $availablePaths, array $dir, string $file): ?string
    {
        $dir = DIRECTORY_SEPARATOR . $this->arrPt->setArray($dir)->getString();
        $dirLen = mb_strlen($dir);
        foreach ($availablePaths as $namespace => $path) {
            // got some path
            $pt = implode(DIRECTORY_SEPARATOR, $path);
            $compLen = min($dirLen, mb_strlen($pt));
            $lgPath = mb_substr(Useful::mb_str_pad($pt, $compLen, '-'), 0, $compLen);
            $lgDir = mb_substr(Useful::mb_str_pad($dir, $compLen, '-'), 0, $compLen);
            if ($lgDir == $lgPath) {
                // rewrite namespace
                $lcDir = DIRECTORY_SEPARATOR == $dir[0] ? $dir : DIRECTORY_SEPARATOR . $dir;
                $end = $namespace . mb_substr($lcDir, $compLen);
                // change slashes
                $namespaced = DIRECTORY_SEPARATOR == mb_substr($end, -1) ? $end : $end . DIRECTORY_SEPARATOR;
                $namespaced = strtr($namespaced, DIRECTORY_SEPARATOR, '\\');
                // remove ext
                $withExt = mb_strripos($file, Interfaces\ISources::EXT_PHP);
                $withoutExt = (false !== $withExt) ? mb_substr($file, 0, $withExt) : $file ;
                // return named class
                return $namespaced . $withoutExt;
            }
        }
        return null;
    }

    public function onlyPhp(string $filename): bool
    {
        $extPos = mb_strripos($filename, Interfaces\ISources::EXT_PHP);
        return mb_substr($filename, 0, $extPos) . Interfaces\ISources::EXT_PHP == $filename; // something more than ext - and die!
    }
}
