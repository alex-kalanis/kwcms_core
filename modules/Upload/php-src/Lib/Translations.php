<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_user_paths\Interfaces\IUPTranslations;
use kalanis\UploadPerPartes\Interfaces\IUppTranslations;


/**
 * Class Translations
 * @package KWCMS\modules\Upload\Lib
 */
class Translations implements IUppTranslations, IUPTranslations
{
    public function upUserNameIsShort(): string
    {
        return Lang::get('chdir.user_dir.username_is_short');
    }

    public function upUserNameContainsChars(): string
    {
        return Lang::get('chdir.user_dir.user_name_contains_chars');
    }

    public function upUserNameNotDefined(): string
    {
        return Lang::get('chdir.user_dir.user_name_not_defined');
    }

    public function upCannotDetermineUserDir(): string
    {
        return Lang::get('chdir.user_dir.cannot_determine_user_dir');
    }

    public function upCannotCreateUserDir(): string
    {
        return Lang::get('chdir.user_dir.cannot_create_user_dir');
    }

    public function upCannotGetFullPaths(): string
    {
        return Lang::get('chdir.user_dir.cannot_get_full_paths');
    }

    public function uppBadResponse(string $responseType): string
    {
        return Lang::get('upload.vendor.sent_file_name_is_empty', $responseType);
    }

    public function uppTargetNotSet(): string
    {
        return Lang::get('upload.vendor.target_not_set');
    }

    public function uppTargetIsWrong(string $url): string
    {
        return Lang::get('upload.vendor.target_is_wrong', $url);
    }

    public function uppChecksumVariantIsWrong(string $variant): string
    {
        return Lang::get('upload.vendor.checksum_is_wrong', $variant);
    }

    public function uppDecoderVariantIsWrong(string $variant): string
    {
        return Lang::get('upload.vendor.decoder_is_wrong', $variant);
    }

    public function uppIncomingDataCannotDecode(): string
    {
        return Lang::get('upload.vendor.cannot_decode_incoming_data');
    }

    public function uppSentNameIsEmpty(): string
    {
        return Lang::get('upload.vendor.sent_file_name_empty');
    }

    public function uppChecksumIsEmpty(): string
    {
        return Lang::get('upload.vendor.checksum_data_is_empty');
    }

    public function uppDataEncoderVariantNotSet(): string
    {
        return Lang::get('upload.vendor.driving_encoder_not_set');
    }

    public function uppDataEncoderVariantIsWrong(string $variant): string
    {
        return Lang::get('upload.vendor.driving_encoder_is_wrong', $variant);
    }

    public function uppDataModifierVariantNotSet(): string
    {
        return Lang::get('upload.vendor.driving_modifier_not_set');
    }

    public function uppDataModifierVariantIsWrong(string $variant): string
    {
        return Lang::get('upload.vendor.driving_modifier_is_wrong', $variant);
    }

    public function uppKeyEncoderVariantNotSet(): string
    {
        return Lang::get('upload.vendor.driving_key_encoder_not_set');
    }

    public function uppKeyEncoderVariantIsWrong(string $className): string
    {
        return Lang::get('upload.vendor.driving_key_encoder_is_wrong', $className);
    }

    public function uppKeyModifierVariantNotSet(): string
    {
        return Lang::get('upload.vendor.driving_key_modifier_not_set');
    }

    public function uppKeyModifierVariantIsWrong(string $className): string
    {
        return Lang::get('upload.vendor.driving_key_modifier_is_wrong', $className);
    }

    public function uppDriveFileStorageNotSet(): string
    {
        return Lang::get('upload.vendor.drive_file_storage_not_set');
    }

    public function uppDriveFileCannotRead(string $key): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_read', $key);
    }

    public function uppDriveFileCannotWrite(string $key): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_write', $key);
    }

    public function uppDriveFileAlreadyExists(string $driveFile): string
    {
        return Lang::get('upload.vendor.drive_file_already_exists', $driveFile);
    }

    public function uppTempEncoderVariantNotSet(): string
    {
        return Lang::get('upload.vendor.temp_encoder_not_set');
    }

    public function uppTempEncoderVariantIsWrong(string $variant): string
    {
        return Lang::get('upload.vendor.temp_encoder_is_wrong', $variant);
    }

    public function uppTempStorageNotSet(): string
    {
        return Lang::get('upload.vendor.temp_storage_not_set');
    }

    public function uppFinalEncoderVariantNotSet(): string
    {
        return Lang::get('upload.vendor.final_encoder_not_set');
    }

    public function uppFinalEncoderVariantIsWrong(string $variant): string
    {
        return Lang::get('upload.vendor.final_encoder_is_wrong');
    }

    public function uppFinalStorageNotSet(): string
    {
        return Lang::get('upload.vendor.final_storage_not_set');
    }

    public function uppCannotReadFile(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_read');
    }

    public function uppCannotWriteFile(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_write');
    }

    public function uppDriveFileCannotRemove(string $key): string
    {
        return Lang::get('upload.vendor.drive_file_cannot_remove');
    }

    public function uppCannotRemoveData(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_remove');
    }

    public function uppCannotTruncateFile(string $location): string
    {
        return Lang::get('upload.vendor.data_cannot_truncate');
    }

    public function uppSegmentOutOfBounds(int $segment): string
    {
        return Lang::get('upload.vendor.segment_out_of_bounds');
    }
}
