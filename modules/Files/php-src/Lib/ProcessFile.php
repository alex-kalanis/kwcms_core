<?php

namespace KWCMS\modules\Files\Lib;


use Error;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Files\FilesException;
use KWCMS\modules\Files\Interfaces\IProcessFiles;


/**
 * Class ProcessFile
 * @package KWCMS\modules\Files\Lib
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    protected $sourcePath = '';

    public function __construct(string $sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    public function uploadFile(FileForm $form): bool
    {
        $entry = $form->getControl('uploadedFile')->getValue();
        if (empty($entry) || !($entry instanceof IFileEntry)) {
            throw new FilesException(Lang::get('files.must_be_sent'));
        }
        try {
            return move_uploaded_file($entry->getTempName(), $this->sourcePath . DIRECTORY_SEPARATOR . $this->findFreeName($entry->getValue()));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage());
        }
    }

    protected function findFreeName(string $name): string
    {
        $name = Stuff::canonize($name);
        $ext = Stuff::fileExt($name);
        if (0 < strlen($ext)) {
            $ext = IPaths::SPLITTER_DOT . $ext;
        }
        $fileName = Stuff::fileBase($name);
        if (!file_exists($this->sourcePath . $fileName . $ext)) {
            return $fileName . $ext;
        }
        $i = 0;
        while (file_exists($this->sourcePath . $fileName . static::FREE_NAME_SEPARATOR . $i . $ext)) {
            $i++;
        }
        return $fileName . static::FREE_NAME_SEPARATOR . $i . $ext;
    }

    public function copyFile(FileForm $form): bool
    {
        $entry = strval($form->getControl('fileName')->getValue());
        $to = strval($form->getControl('targetPath')->getValue());
        $fileName = Stuff::filename($entry);
        try {
            return copy(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry,
                strval($to) . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage());
        }
    }

    public function moveFile(FileForm $form): bool
    {
        $entry = strval($form->getControl('fileName')->getValue());
        $to = strval($form->getControl('targetPath')->getValue());
        $fileName = Stuff::filename($entry);
        try {
            return rename(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry,
                strval($to) . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage());
        }
    }

    public function renameFile(FileForm $form): bool
    {
        $entry = strval($form->getControl('fileName')->getValue());
        $to = strval($form->getControl('targetPath')->getValue());
        try {
            return rename(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry,
                $this->sourcePath . DIRECTORY_SEPARATOR . $to
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage());
        }
    }

    public function deleteFile(FileForm $form): bool
    {
        $entry = strval($form->getControl('fileName')->getValue());
        try {
            return unlink(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage());
        }
    }
}
