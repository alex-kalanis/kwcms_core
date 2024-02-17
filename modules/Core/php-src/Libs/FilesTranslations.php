<?php

namespace KWCMS\modules\Core\Libs;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_langs\Lang;


/**
 * Class FilesTranslations
 * @package KWCMS\modules\Core\Libs
 */
class FilesTranslations implements IFLTranslations
{
    public function flCannotProcessNode(string $name): string
    {
        return Lang::get('core.files.cannot_process_path', $name);
    }

    public function flCannotLoadFile(string $fileName): string
    {
        return Lang::get('core.files.cannot_load_file', $fileName);
    }

    public function flCannotSaveFile(string $fileName): string
    {
        return Lang::get('core.files.cannot_save_file', $fileName);
    }

    public function flCannotOpenFile(string $fileName): string
    {
        return Lang::get('core.files.cannot_open_file', $fileName);
    }

    public function flCannotWriteFile(string $fileName): string
    {
        return Lang::get('core.files.cannot_write_file', $fileName);
    }

    public function flCannotGetFilePart(string $fileName): string
    {
        return Lang::get('core.files.cannot_extract_content', $fileName);
    }

    public function flCannotGetSize(string $fileName): string
    {
        return Lang::get('core.files.cannot_get_file_size', $fileName);
    }

    public function flCannotCopyFile(string $sourceFileName, string $destFileName): string
    {
        return Lang::get('core.files.cannot_copy_file', $sourceFileName, $destFileName);
    }

    public function flCannotMoveFile(string $sourceFileName, string $destFileName): string
    {
        return Lang::get('core.files.cannot_move_file', $sourceFileName, $destFileName);
    }

    public function flCannotRemoveFile(string $fileName): string
    {
        return Lang::get('core.files.cannot_remove_file', $fileName);
    }

    public function flCannotCreateDir(string $dirName): string
    {
        return Lang::get('core.files.cannot_create_dir', $dirName);
    }

    public function flCannotReadDir(string $dirName): string
    {
        return Lang::get('core.files.cannot_read_dir', $dirName);
    }

    public function flCannotCopyDir(string $sourceDirName, string $destDirName): string
    {
        return Lang::get('core.files.cannot_copy_dir', $sourceDirName, $destDirName);
    }

    public function flCannotMoveDir(string $sourceDirName, string $destDirName): string
    {
        return Lang::get('core.files.cannot_move_dir', $sourceDirName, $destDirName);
    }

    public function flCannotRemoveDir(string $dirName): string
    {
        return Lang::get('core.files.cannot_remove_dir', $dirName);
    }

    public function flNoDirectoryDelimiterSet(): string
    {
        return Lang::get('core.files.empty_delimiter');
    }

    public function flNoProcessNodeSet(): string
    {
        return Lang::get('core.files.no_process_nodes');
    }

    public function flNoProcessFileSet(): string
    {
        return Lang::get('core.files.no_process_files');
    }

    public function flNoProcessDirSet(): string
    {
        return Lang::get('core.files.no_process_dirs');
    }

    public function flNoAvailableClasses(): string
    {
        return Lang::get('core.files.no_available_classes');
    }
}
