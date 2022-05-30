<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Files\Lib
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Files';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_DASHBOARD}', '#');
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

        $this->addInput('{TEXT_DASHBOARD}', Lang::get('files.dashboard.short'));
        $this->addInput('{TEXT_FILES}', Lang::get('files.files'));
        $this->addInput('{TEXT_DIRS}', Lang::get('files.dirs'));
        $this->addInput('{TEXT_FILE_UPLOAD}', Lang::get('files.file.upload.short'));
        $this->addInput('{TEXT_FILE_COPY}', Lang::get('files.file.copy.short'));
        $this->addInput('{TEXT_FILE_MOVE}', Lang::get('files.file.move.short'));
        $this->addInput('{TEXT_FILE_RENAME}', Lang::get('files.file.rename.short'));
        $this->addInput('{TEXT_FILE_DELETE}', Lang::get('files.file.delete.short'));
        $this->addInput('{TEXT_FILE_READ}', Lang::get('files.file.read.short'));
        $this->addInput('{TEXT_DIR_NEW}', Lang::get('files.dir.create.short'));
        $this->addInput('{TEXT_DIR_COPY}', Lang::get('files.dir.copy.short'));
        $this->addInput('{TEXT_DIR_MOVE}', Lang::get('files.dir.move.short'));
        $this->addInput('{TEXT_DIR_RENAME}', Lang::get('files.dir.rename.short'));
        $this->addInput('{TEXT_DIR_DELETE}', Lang::get('files.dir.delete.short'));
        $this->addInput('{TEXT_CHANGE_DIRECTORY}', Lang::get('dashboard.dir_select'));
    }

    public function setData(
        string $content, string $linkDashboard, string $linkFileUpload, string $linkFileCopy, string $linkFileMove,
        string $linkFileRename, string $linkFileDelete, string $linkFileRead,
        string $linkDirNew, string $linkDirCopy, string $linkDirMove, string $linkDirRename, string $linkDirDelete, string $linkChDir
    ): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_DASHBOARD}', $linkDashboard);
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
}
