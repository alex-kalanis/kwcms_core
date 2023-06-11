<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\kw_langs\Lang;
use kalanis\UploadPerPartes\Interfaces\IUPPTranslations;


/**
 * Class Translations
 * @package KWCMS\modules\Upload\Lib
 */
class Translations implements IUPPTranslations
{
    public function uppSentNameIsEmpty(): string
    {
        return Lang::get('upload.vendor.sent_file_name_is_empty');
    }

    public function uppUploadNameIsEmpty(): string
    {
        return Lang::get('upload.vendor.upload_file_name_is_empty');
    }

    public function uppSharedKeyIsEmpty(): string
    {
        return Lang::get('upload.vendor.shared_key_is_empty');
    }

    public function uppSharedKeyIsInvalid(): string
    {
        return Lang::get('upload.vendor.shared_key_is_invalid');
    }

    public function uppKeyVariantNotSet(): string
    {
        return Lang::get('upload.vendor.key_variant_not_known');
    }

    public function uppTargetDirIsEmpty(): string
    {
        return Lang::get('upload.vendor.target_dir_is_empty');
    }

    public function uppDriveFileAlreadyExists(string $driveFile): string
    {
        return Lang::get('upload.vendor.drive_file_already_exists');
    }

    public function uppDriveFileNotContinuous(string $driveFile): string
    {
        return Lang::get('upload.vendor.drive_file_not_continuous');
    }

    public function uppDriveFileCannotRemove(string $key): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_remove');
    }

    public function uppDriveFileVariantNotSet(): string
    {
        return Lang::get('upload.vendor.drive_file_variant_not_known');
    }

    public function uppDriveFileCannotRead(string $key): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_read');
    }

    public function uppDriveFileCannotWrite(string $key): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_write');
    }

    public function uppCannotRemoveData(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_remove');
    }

    public function uppReadTooEarly(string $key): string
    {
        return Lang::get('upload.vendor.data_read_early');
    }

    public function uppCannotOpenFile(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_open');
    }

    public function uppCannotReadFile(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_read');
    }

    public function uppCannotSeekFile(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_seek');
    }

    public function uppCannotWriteFile(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_write');
    }

    public function uppCannotTruncateFile(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_truncate');
    }

    public function uppSegmentOutOfBounds(int $segment): string
    {
        return Lang::get('upload.vendor.segment_out_of_bounds');
    }

    public function uppSegmentNotUploadedYet(int $segment): string
    {
        return Lang::get('upload.vendor.segment_not_uploaded');
    }

    public function uppKeyVariantIsWrong(string $className): string
    {
        return Lang::get('upload.vendor.key_variant_is_wrong');
    }

    public function uppDriveFileVariantIsWrong(string $className): string
    {
        return Lang::get('upload.vendor.file_variant_is_wrong');
    }
}
