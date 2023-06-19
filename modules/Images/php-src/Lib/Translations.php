<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_langs\Lang;
use kalanis\kw_user_paths\Interfaces\IUPTranslations;


/**
 * Class Translations
 * @package KWCMS\modules\Images\Lib
 */
class Translations implements IUPTranslations, IIMTranslations
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

    public function imGdLibNotPresent(): string
    {
        return Lang::get('images.images.no_gd_library');
    }

    public function imImageMagicLibNotPresent(): string
    {
        return Lang::get('images.images.no_imagemagick');
    }

    public function imCannotCreateFromResource(): string
    {
        return Lang::get('images.images.cannot_create_from_resource');
    }

    public function imCannotSaveResource(): string
    {
        return Lang::get('images.images.cannot_save_resource');
    }

    public function imUnknownType(string $mime): string
    {
        return Lang::get('images.images.unknown_type', $mime);
    }

    public function imWrongInstance(string $class): string
    {
        return Lang::get('images.images.bad_instance', $class);
    }

    public function imWrongMime(string $mime): string
    {
        return Lang::get('images.images.wrong_file_mime', $mime);
    }

    public function imImageCannotResize(): string
    {
        return Lang::get('images.images.image_cannot_be_resized');
    }

    public function imSizesNotSet(): string
    {
        return Lang::get('images.images.compare_size_not_set');
    }

    public function imImageCannotResample(): string
    {
        return Lang::get('images.images.image_cannot_be_resampled');
    }

    public function imImageCannotCreateEmpty(): string
    {
        return Lang::get('images.images.image_cannot_create_empty');
    }

    public function imImageCannotGetSize(): string
    {
        return Lang::get('images.images.image_cannot_get_size');
    }

    public function imImageLoadFirst(): string
    {
        return Lang::get('images.images.image_load_first');
    }

    public function imDescCannotRemove(): string
    {
        return Lang::get('images.images.description_cannot_remove');
    }

    public function imDescCannotFind(): string
    {
        return Lang::get('images.images.description_cannot_find');
    }

    public function imDescAlreadyExistsHere(): string
    {
        return Lang::get('images.images.description_already_exists');
    }

    public function imDescCannotRemoveOld(): string
    {
        return Lang::get('images.images.description_cannot_remove_old');
    }

    public function imDescCannotCopyBase(): string
    {
        return Lang::get('images.images.description_cannot_copy');
    }

    public function imDescCannotMoveBase(): string
    {
        return Lang::get('images.images.description_cannot_move');
    }

    public function imDescCannotRenameBase(): string
    {
        return Lang::get('images.images.description_cannot_rename');
    }

    public function imDirThumbCannotRemove(): string
    {
        return Lang::get('images.images.thumb_dir_cannot_remove');
    }

    public function imDirThumbCannotRemoveCurrent(): string
    {
        return Lang::get('images.images.thumb_cannot_remove_current');
    }

    public function imImageSizeExists(): string
    {
        return Lang::get('images.images.image_cannot_read_size');
    }

    public function imImageSizeTooLarge(): string
    {
        return Lang::get('images.images.image_too_large');
    }

    public function imImageCannotFind(): string
    {
        return Lang::get('images.images.image_cannot_find');
    }

    public function imImageCannotRemove(): string
    {
        return Lang::get('images.images.image_cannot_remove');
    }

    public function imImageAlreadyExistsHere(): string
    {
        return Lang::get('images.images.image_already_exists');
    }

    public function imImageCannotRemoveOld(): string
    {
        return Lang::get('images.images.image_cannot_remove_old');
    }

    public function imImageCannotCopyBase(): string
    {
        return Lang::get('images.images.image_cannot_copy');
    }

    public function imImageCannotMoveBase(): string
    {
        return Lang::get('images.images.image_cannot_move');
    }

    public function imImageCannotRenameBase(): string
    {
        return Lang::get('images.images.image_cannot_rename');
    }

    public function imThumbCannotFind(): string
    {
        return Lang::get('images.images.thumb_cannot_find');
    }

    public function imThumbCannotRemove(): string
    {
        return Lang::get('images.images.thumb_cannot_remove');
    }

    public function imThumbAlreadyExistsHere(): string
    {
        return Lang::get('images.images.thumb_already_exists');
    }

    public function imThumbCannotRemoveOld(): string
    {
        return Lang::get('images.images.thumb_cannot_remove_old');
    }

    public function imThumbCannotGetBaseImage(): string
    {
        return Lang::get('images.images.image_cannot_get_base');
    }

    public function imThumbCannotStoreTemporaryImage(): string
    {
        return Lang::get('images.images.image_cannot_store_temp');
    }

    public function imThumbCannotLoadTemporaryImage(): string
    {
        return Lang::get('images.images.image_cannot_load_temp');
    }

    public function imThumbCannotCopyBase(): string
    {
        return Lang::get('images.images.thumb_cannot_copy');
    }

    public function imThumbCannotMoveBase(): string
    {
        return Lang::get('images.images.thumb_cannot_move');
    }

    public function imThumbCannotRenameBase(): string
    {
        return Lang::get('images.images.thumb_cannot_rename');
    }
}
