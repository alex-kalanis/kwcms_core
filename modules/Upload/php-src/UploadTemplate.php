<?php

namespace KWCMS\modules\Upload;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


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
        $this->addInput('{INIT_PATH}', '//upload-file/init/');
        $this->addInput('{CHECK_PATH}', '//upload-file/check/');
        $this->addInput('{CANCEL_PATH}', '//upload-file/cancel/');
        $this->addInput('{TRIM_PATH}', '//upload-file/trim/');
        $this->addInput('{FILE_PATH}', '//upload-file/file/');
        $this->addInput('{DONE_PATH}', '//upload-file/done/');

        $this->addInput('{BUTTON_START}', Lang::get('upload.button.start'));
        $this->addInput('{BUTTON_ABORT}', Lang::get('upload.button.abort'));
        $this->addInput('{BUTTON_CLEAR}', Lang::get('upload.button.clear'));
        $this->addInput('{BUTTON_RETRY}', Lang::get('upload.button.retry'));
        $this->addInput('{BUTTON_RESUME}', Lang::get('upload.button.resume'));
        $this->addInput('{BUTTON_STOP}', Lang::get('upload.button.stop'));

        $this->addInput('{FILE_NAME}', Lang::get('upload.form.file_name'));
        $this->addInput('{LANG_ELAPSED_TIME}', Lang::get('upload.form.elapsed_time'));
        $this->addInput('{LANG_ESTIMATED_TIME}', Lang::get('upload.form.estimated_time'));
        $this->addInput('{LANG_ESTIMATED_SPEED}', Lang::get('upload.form.estimated_speed'));

        $this->addInput('{SCRIPT_READ_FILE_CANNOT_SLICE}', Lang::get('upload.script.read_file_cannot_slice'));
        $this->addInput('{SCRIPT_INIT_RETURNS_ERROR}', Lang::get('upload.script.init_returns_following_error'));
        $this->addInput('{SCRIPT_INIT_RETURNS_FAIL}', Lang::get('upload.script.init_returns_something_failed'));
        $this->addInput('{SCRIPT_CHECK_RETURNS_FAIL}', Lang::get('upload.script.checker_returns_something_failed'));
        $this->addInput('{SCRIPT_DATA_RETURNS_FAIL}', Lang::get('upload.script.data_upload_returns_something_failed'));
        $this->addInput('{SCRIPT_DONE_RETURNS_FAIL}', Lang::get('upload.script.done_returns_something_failed'));

        $this->addInput('{VALUE_ELAPSED_TIME}', '');
        $this->addInput('{VALUE_ESTIMATED_TIME}', '');
        $this->addInput('{VALUE_CURRENT_POSITION}', '');
        $this->addInput('{VALUE_TOTAL_LENGTH}', '');
        $this->addInput('{VALUE_ESTIMATED_SPEED}', '');
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
