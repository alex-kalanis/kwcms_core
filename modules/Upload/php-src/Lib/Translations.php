<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\kw_langs\Lang;
use kalanis\UploadPerPartes\Uploader;


/**
 * Class Translations
 * @package KWCMS\modules\Upload\Lib
 */
class Translations extends Uploader\Translations
{
    public function sentNameIsEmpty(): string
    {
        return Lang::get('upload.vendor.sent_file_name_is_empty');
    }

    public function uploadNameIsEmpty(): string
    {
        return Lang::get('upload.vendor.upload_file_name_is_empty');
    }

    public function sharedKeyIsEmpty(): string
    {
        return Lang::get('upload.vendor.shared_key_is_empty');
    }

    public function sharedKeyIsInvalid(): string
    {
        return Lang::get('upload.vendor.shared_key_is_invalid');
    }

    public function keyVariantNotSet(): string
    {
        return Lang::get('upload.vendor.key_variant_not_known');
    }

    public function targetDirIsEmpty(): string
    {
        return Lang::get('upload.vendor.target_dir_is_empty');
    }

    public function driveFileAlreadyExists(): string
    {
        return Lang::get('upload.vendor.drive_file_already_exists');
    }

    public function driveFileNotContinuous(): string
    {
        return Lang::get('upload.vendor.drive_file_not_continuous');
    }

    public function driveFileCannotRemove(): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_remove');
    }

    public function driveFileVariantNotSet(): string
    {
        return Lang::get('upload.vendor.drive_file_variant_not_known');
    }

    public function driveFileCannotRead(): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_read');
    }

    public function driveFileCannotWrite(): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_write');
    }

    public function cannotRemoveData(): string
    {
        return Lang::get('upload.vendor.data_cannot_remove');
    }

    public function readTooEarly(): string
    {
        return Lang::get('upload.vendor.data_read_early');
    }

    public function cannotOpenFile(): string
    {
        return Lang::get('upload.vendor.data_cannot_open');
    }

    public function cannotReadFile(): string
    {
        return Lang::get('upload.vendor.data_cannot_read');
    }

    public function cannotSeekFile(): string
    {
        return Lang::get('upload.vendor.data_cannot_seek');
    }

    public function cannotWriteFile(): string
    {
        return Lang::get('upload.vendor.data_cannot_write');
    }

    public function cannotTruncateFile(): string
    {
        return Lang::get('upload.vendor.data_cannot_truncate');
    }

    public function segmentOutOfBounds(): string
    {
        return Lang::get('upload.vendor.segment_out_of_bounds');
    }

    public function segmentNotUploadedYet(): string
    {
        return Lang::get('upload.vendor.segment_not_uploaded');
    }
}
