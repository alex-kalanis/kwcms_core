<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class DashboardTemplate
 * @package KWCMS\modules\Files\Lib
 */
class DashboardTemplate extends ATemplate
{
    protected $moduleName = 'Files';
    protected $templateName = 'dashboard';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK_FILE_UPLOAD}', '#');
        $this->addInput('{LINK_FILE_COPY}', '#');
        $this->addInput('{LINK_FILE_MOVE}', '#');
        $this->addInput('{LINK_FILE_RENAME}', '#');
        $this->addInput('{LINK_FILE_DELETE}', '#');
        $this->addInput('{LINK_FILE_READ}', '#');
        $this->addInput('{LINK_DIR_NEW}', '#');
        $this->addInput('{LINK_DIR_COPY}', '#');
        $this->addInput('{LINK_DIR_MOVE}', '#');
        $this->addInput('{LINK_DIR_RENAME}', '#');
        $this->addInput('{LINK_DIR_DELETE}', '#');
        $this->addInput('{LINK_CHDIR}', '#');

        $this->addInput('{IMAGE_FILE_UPLOAD}', '#');
        $this->addInput('{IMAGE_FILE_COPY}', '#');
        $this->addInput('{IMAGE_FILE_MOVE}', '#');
        $this->addInput('{IMAGE_FILE_RENAME}', '#');
        $this->addInput('{IMAGE_FILE_DELETE}', '#');
        $this->addInput('{IMAGE_FILE_READ}', '#');
        $this->addInput('{IMAGE_DIR_NEW}', '#');
        $this->addInput('{IMAGE_DIR_COPY}', '#');
        $this->addInput('{IMAGE_DIR_MOVE}', '#');
        $this->addInput('{IMAGE_DIR_RENAME}', '#');
        $this->addInput('{IMAGE_DIR_DELETE}', '#');
        $this->addInput('{IMAGE_CHDIR}', '#');

        $this->addInput('{TEXT_FILES}', Lang::get('files.files'));
        $this->addInput('{TEXT_DIRS}', Lang::get('files.dirs'));
        $this->addInput('{TEXT_FILE_UPLOAD}', Lang::get('files.file.upload'));
        $this->addInput('{TEXT_FILE_COPY}', Lang::get('files.file.copy'));
        $this->addInput('{TEXT_FILE_MOVE}', Lang::get('files.file.move'));
        $this->addInput('{TEXT_FILE_RENAME}', Lang::get('files.file.rename'));
        $this->addInput('{TEXT_FILE_DELETE}', Lang::get('files.file.delete'));
        $this->addInput('{TEXT_FILE_READ}', Lang::get('files.file.read'));
        $this->addInput('{TEXT_DIR_NEW}', Lang::get('files.dir.create'));
        $this->addInput('{TEXT_DIR_COPY}', Lang::get('files.dir.copy'));
        $this->addInput('{TEXT_DIR_MOVE}', Lang::get('files.dir.move'));
        $this->addInput('{TEXT_DIR_RENAME}', Lang::get('files.dir.rename'));
        $this->addInput('{TEXT_DIR_DELETE}', Lang::get('files.dir.delete'));
        $this->addInput('{TEXT_CHANGE_DIRECTORY}', Lang::get('dashboard.dir_select'));
    }

    public function setLinks(
        string $linkFileUpload, string $linkFileCopy, string $linkFileMove, string $linkFileRename, string $linkFileDelete, string $linkFileRead,
        string $linkDirNew, string $linkDirCopy, string $linkDirMove, string $linkDirRename, string $linkDirDelete, string $linkChDir
    ): self
    {
        $this->updateItem('{LINK_FILE_UPLOAD}', $linkFileUpload);
        $this->updateItem('{LINK_FILE_COPY}', $linkFileCopy);
        $this->updateItem('{LINK_FILE_MOVE}', $linkFileMove);
        $this->updateItem('{LINK_FILE_RENAME}', $linkFileRename);
        $this->updateItem('{LINK_FILE_DELETE}', $linkFileDelete);
        $this->updateItem('{LINK_FILE_READ}', $linkFileRead);
        $this->updateItem('{LINK_DIR_NEW}', $linkDirNew);
        $this->updateItem('{LINK_DIR_COPY}', $linkDirCopy);
        $this->updateItem('{LINK_DIR_MOVE}', $linkDirMove);
        $this->updateItem('{LINK_DIR_RENAME}', $linkDirRename);
        $this->updateItem('{LINK_DIR_DELETE}', $linkDirDelete);
        $this->updateItem('{LINK_CHDIR}', $linkChDir);
        return $this;
    }

    public function setImages(
        string $linkFileUpload, string $linkFileCopy, string $linkFileMove, string $linkFileRename, string $linkFileDelete, string $linkFileRead,
        string $linkDirNew, string $linkDirCopy, string $linkDirMove, string $linkDirRename, string $linkDirDelete, string $linkChDir
    ): self
    {
        $this->updateItem('{IMAGE_FILE_UPLOAD}', $linkFileUpload);
        $this->updateItem('{IMAGE_FILE_COPY}', $linkFileCopy);
        $this->updateItem('{IMAGE_FILE_MOVE}', $linkFileMove);
        $this->updateItem('{IMAGE_FILE_RENAME}', $linkFileRename);
        $this->updateItem('{IMAGE_FILE_DELETE}', $linkFileDelete);
        $this->updateItem('{IMAGE_FILE_READ}', $linkFileRead);
        $this->updateItem('{IMAGE_DIR_NEW}', $linkDirNew);
        $this->updateItem('{IMAGE_DIR_COPY}', $linkDirCopy);
        $this->updateItem('{IMAGE_DIR_MOVE}', $linkDirMove);
        $this->updateItem('{IMAGE_DIR_RENAME}', $linkDirRename);
        $this->updateItem('{IMAGE_DIR_DELETE}', $linkDirDelete);
        $this->updateItem('{IMAGE_CHDIR}', $linkChDir);
        return $this;
    }
}
