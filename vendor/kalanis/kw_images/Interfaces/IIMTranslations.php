<?php

namespace kalanis\kw_images\Interfaces;


/**
 * Interface IIMTranslations
 * @package kalanis\kw_images\Interfaces
 */
interface IIMTranslations
{
    public function imGdLibNotPresent(): string;

    public function imImageMagicLibNotPresent(): string;

    public function imCannotCreateFromResource(): string;

    public function imCannotSaveResource(): string;

    public function imUnknownType(string $mime): string;

    public function imWrongInstance(string $class): string;

    public function imWrongMime(string $mime): string;

    public function imImageCannotResize(): string;

    public function imSizesNotSet(): string;

    public function imImageCannotResample(): string;

    public function imImageCannotCreateEmpty(): string;

    public function imImageCannotGetSize(): string;

    public function imImageLoadFirst(): string;

    public function imDescCannotRemove(): string;

    public function imDescCannotFind(): string;

    public function imDescAlreadyExistsHere(): string;

    public function imDescCannotRemoveOld(): string;

    public function imDescCannotCopyBase(): string;

    public function imDescCannotMoveBase(): string;

    public function imDescCannotRenameBase(): string;

    public function imDirThumbCannotRemove(): string;

    public function imDirThumbCannotRemoveCurrent(): string;

    public function imImageSizeExists(): string;

    public function imImageSizeTooLarge(): string;

    public function imImageCannotFind(): string;

    public function imImageCannotRemove(): string;

    public function imImageAlreadyExistsHere(): string;

    public function imImageCannotRemoveOld(): string;

    public function imImageCannotCopyBase(): string;

    public function imImageCannotMoveBase(): string;

    public function imImageCannotRenameBase(): string;

    public function imThumbCannotFind(): string;

    public function imThumbCannotRemove(): string;

    public function imThumbAlreadyExistsHere(): string;

    public function imThumbCannotRemoveOld(): string;

    public function imThumbCannotGetBaseImage(): string;

    public function imThumbCannotStoreTemporaryImage(): string;

    public function imThumbCannotLoadTemporaryImage(): string;

    public function imThumbCannotCopyBase(): string;

    public function imThumbCannotMoveBase(): string;

    public function imThumbCannotRenameBase(): string;
}
