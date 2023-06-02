<?php

namespace kalanis\kw_files\Interfaces;


/**
 * Interface IFLTranslations
 * @package kalanis\kw_files\Interfaces
 * Translations
 */
interface IFLTranslations
{
    public function flCannotProcessNode(string $name): string;

    public function flCannotLoadFile(string $fileName): string;

    public function flCannotSaveFile(string $fileName): string;

    public function flCannotOpenFile(string $fileName): string;

    public function flCannotWriteFile(string $fileName): string;

    public function flCannotGetFilePart(string $fileName): string;

    public function flCannotGetSize(string $fileName): string;

    public function flCannotCopyFile(string $sourceFileName, string $destFileName): string;

    public function flCannotMoveFile(string $sourceFileName, string $destFileName): string;

    public function flCannotRemoveFile(string $fileName): string;

    public function flCannotCreateDir(string $dirName): string;

    public function flCannotReadDir(string $dirName): string;

    public function flCannotCopyDir(string $sourceDirName, string $destDirName): string;

    public function flCannotMoveDir(string $sourceDirName, string $destDirName): string;

    public function flCannotRemoveDir(string $dirName): string;

    public function flNoDirectoryDelimiterSet(): string;

    public function flNoProcessNodeSet(): string;

    public function flNoProcessFileSet(): string;

    public function flNoProcessDirSet(): string;

    public function flNoAvailableClasses(): string;
}
