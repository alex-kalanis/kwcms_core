<?php

namespace kalanis\kw_files;


use kalanis\kw_files\Interfaces\IFLTranslations;


/**
 * Class Translations
 * @package kalanis\kw_files
 * Translations
 */
class Translations implements IFLTranslations
{
    public function flCannotProcessNode(string $name): string
    {
        return 'Cannot process wanted path.';
    }

    public function flCannotLoadFile(string $fileName): string
    {
        return 'Cannot load wanted file.';
    }

    public function flCannotSaveFile(string $fileName): string
    {
        return 'Cannot save wanted file.';
    }

    /**
     * @param string $fileName
     * @return string
     * @codeCoverageIgnore failing streams
     */
    public function flCannotOpenFile(string $fileName): string
    {
        return 'Cannot open wanted file.';
    }

    /**
     * @param string $fileName
     * @return string
     * @codeCoverageIgnore failing streams
     */
    public function flCannotWriteFile(string $fileName): string
    {
        return 'Cannot write wanted file.';
    }

    /**
     * @param string $fileName
     * @return string
     * @codeCoverageIgnore failing streams
     */
    public function flCannotGetFilePart(string $fileName): string
    {
        return 'Cannot extract part of content';
    }

    /**
     * @param string $fileName
     * @return string
     * @codeCoverageIgnore failing streams
     */
    public function flCannotGetSize(string $fileName): string
    {
        return 'Cannot copy streams, cannot get file size';
    }

    public function flCannotCopyFile(string $sourceFileName, string $destFileName): string
    {
        return 'Cannot copy file to destination';
    }

    public function flCannotMoveFile(string $sourceFileName, string $destFileName): string
    {
        return 'Cannot move file to destination';
    }

    public function flCannotRemoveFile(string $fileName): string
    {
        return 'Cannot remove file';
    }

    public function flCannotCreateDir(string $dirName): string
    {
        return 'Cannot create directory';
    }

    public function flCannotReadDir(string $dirName): string
    {
        return 'Cannot read directory';
    }

    public function flCannotCopyDir(string $sourceDirName, string $destDirName): string
    {
        return 'Cannot copy directory to destination';
    }

    public function flCannotMoveDir(string $sourceDirName, string $destDirName): string
    {
        return 'Cannot move directory to destination';
    }

    public function flCannotRemoveDir(string $dirName): string
    {
        return 'Cannot remove directory';
    }

    /**
     * @return string
     * @codeCoverageIgnore only when path fails
     */
    public function flNoDirectoryDelimiterSet(): string
    {
        return 'You set the empty directory delimiter!';
    }

    public function flNoProcessNodeSet(): string
    {
        return 'No processing nodes library set!';
    }

    public function flNoProcessFileSet(): string
    {
        return 'No processing files library set!';
    }

    public function flNoProcessDirSet(): string
    {
        return 'No processing directories library set!';
    }

    public function flNoAvailableClasses(): string
    {
        return 'No available classes for that settings!';
    }
}
