<?php

namespace KWCMS\modules\Upload;


use kalanis\kw_modules\ATemplate;


/**
 * Class UploadTemplate
 * @package KWCMS\modules\Upload
 */
class UploadTemplate extends ATemplate
{
    protected $moduleName = 'Upload';
    protected $templateName = 'page';

    protected function fillInputs(): void
    {
        $this->addInput('{INIT_PATH}', "//upload-file/init/");
        $this->addInput('{CHECK_PATH}', "//upload-file/check/");
        $this->addInput('{CANCEL_PATH}', "//upload-file/cancel/");
        $this->addInput('{TRIM_PATH}', "//upload-file/trim/");
        $this->addInput('{FILE_PATH}', "//upload-file/file/");
        $this->addInput('{DONE_PATH}', "//upload-file/done/");
    }

    public function setData(string $initPath, string $checkPath, string $cancelPath, string $trimPath, string $filePath, string $donePath): self
    {
        $this->updateItem('{INIT_PATH}', $initPath);
        $this->updateItem('{CHECK_PATH}', $checkPath);
        $this->updateItem('{CANCEL_PATH}', $cancelPath);
        $this->updateItem('{TRIM_PATH}', $trimPath);
        $this->updateItem('{FILE_PATH}', $filePath);
        $this->updateItem('{DONE_PATH}', $donePath);
        return $this;
    }
}
