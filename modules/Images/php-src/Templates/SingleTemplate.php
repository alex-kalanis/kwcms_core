<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;
use KWCMS\modules\Images\Forms;


/**
 * Class SingleTemplate
 * @package KWCMS\modules\Menu\Lib
 */
class SingleTemplate extends ATemplate
{
    protected $moduleName = 'Images';
    protected $templateName = 'single_image';

    protected function fillInputs(): void
    {
        $this->addInput('{FULL_IMAGE}', '#');
        $this->addInput('{THUMB_PATH}', '#');
        $this->addInput('{THUMB_DESCRIPTION}', Lang::get('images.single.thumb'));
        $this->addInput('{FORM_THUMB_START}');
        $this->addInput('{INPUT_THUMB_SUBMIT}');
        $this->addInput('{FORM_THUMB_END}');
        $this->addInput('{TITLE_DESC}', Lang::get('images.single.desc'));
        $this->addInput('{FORM_DESC_START}');
        $this->addInput('{LABEL_DESC}');
        $this->addInput('{INPUT_DESC_FIELD}');
        $this->addInput('{INPUT_DESC_SUBMIT}');
        $this->addInput('{INPUT_DESC_RESET}');
        $this->addInput('{FORM_DESC_END}');
        $this->addInput('{TITLE_RENAME}', Lang::get('images.single.rename'));
        $this->addInput('{FORM_RENAME_START}');
        $this->addInput('{LABEL_RENAME}');
        $this->addInput('{INPUT_RENAME_FIELD}');
        $this->addInput('{INPUT_RENAME_SUBMIT}');
        $this->addInput('{INPUT_RENAME_RESET}');
        $this->addInput('{FORM_RENAME_END}');
        $this->addInput('{TITLE_COPY}', Lang::get('images.single.copy'));
        $this->addInput('{FORM_COPY_START}');
        $this->addInput('{LABEL_COPY}');
        $this->addInput('{INPUT_COPY_FIELD}');
        $this->addInput('{INPUT_COPY_SUBMIT}');
        $this->addInput('{INPUT_COPY_RESET}');
        $this->addInput('{FORM_COPY_END}');
        $this->addInput('{TITLE_MOVE}', Lang::get('images.single.move'));
        $this->addInput('{FORM_MOVE_START}');
        $this->addInput('{LABEL_MOVE}');
        $this->addInput('{INPUT_MOVE_FIELD}');
        $this->addInput('{INPUT_MOVE_SUBMIT}');
        $this->addInput('{INPUT_MOVE_RESET}');
        $this->addInput('{FORM_MOVE_END}');
        $this->addInput('{TITLE_PRIMARY}', Lang::get('images.single.primary_thumb'));
        $this->addInput('{FORM_PRIMARY_START}');
        $this->addInput('{INPUT_PRIMARY_SUBMIT}');
        $this->addInput('{FORM_PRIMARY_END}');
        $this->addInput('{TITLE_DELETE}', Lang::get('images.single.delete'));
        $this->addInput('{FORM_DELETE_START}');
        $this->addInput('{INPUT_DELETE_SUBMIT}');
        $this->addInput('{FORM_DELETE_END}');
    }

    /**
     * @param string $fullLink
     * @param string $thumbLink
     * @param Forms\FileThumbForm $formThumb
     * @param Forms\DescForm $formDesc
     * @param Forms\FileRenameForm $formRename
     * @param Forms\FileActionForm $formCopy
     * @param Forms\FileActionForm $formMove
     * @param Forms\FileThumbForm $formPrimary
     * @param Forms\FileDeleteForm $formDelete
     * @return SingleTemplate
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(string $fullLink, string $thumbLink, Forms\FileThumbForm $formThumb, Forms\DescForm $formDesc, Forms\FileRenameForm $formRename, Forms\FileActionForm $formCopy, Forms\FileActionForm $formMove, Forms\FileThumbForm $formPrimary, Forms\FileDeleteForm $formDelete): self
    {
        $this->updateItem('{FULL_IMAGE}', $fullLink);
        $this->updateItem('{THUMB_PATH}', $thumbLink);
        $this->updateItem('{FORM_THUMB_START}', $formThumb->renderStart());
        $this->updateItem('{INPUT_THUMB_SUBMIT}', $formThumb->getControl('selectFile')->renderInput());
        $this->updateItem('{FORM_THUMB_END}', $formThumb->renderEnd());
        $this->updateItem('{FORM_DESC_START}', $formDesc->renderStart());
        $this->updateItem('{LABEL_DESC}', $formDesc->getControl('description')->renderLabel());
        $this->updateItem('{INPUT_DESC_FIELD}', $formDesc->getControl('description')->renderInput());
        $this->updateItem('{INPUT_DESC_SUBMIT}', $formDesc->getControl('saveDesc')->renderInput());
        $this->updateItem('{INPUT_DESC_RESET}', $formDesc->getControl('resetDesc')->renderInput());
        $this->updateItem('{FORM_DESC_END}', $formDesc->renderEnd());
        $this->updateItem('{FORM_RENAME_START}', $formRename->renderStart());
        $this->updateItem('{LABEL_RENAME}', $formRename->getControl('newName')->renderLabel());
        $this->updateItem('{INPUT_RENAME_FIELD}', $formRename->getControl('newName')->renderInput());
        $this->updateItem('{INPUT_RENAME_SUBMIT}', $formRename->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RENAME_RESET}', $formRename->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_RENAME_END}', $formRename->renderEnd());
        $this->updateItem('{FORM_COPY_START}', $formCopy->renderStart());
        $this->updateItem('{LABEL_COPY}', $formCopy->getControl('where')->renderLabel());
        $this->updateItem('{INPUT_COPY_FIELD}', $formCopy->getControl('where')->renderInput());
        $this->updateItem('{INPUT_COPY_SUBMIT}', $formCopy->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_COPY_RESET}', $formCopy->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_COPY_END}', $formCopy->renderEnd());
        $this->updateItem('{FORM_MOVE_START}', $formMove->renderStart());
        $this->updateItem('{LABEL_MOVE}', $formMove->getControl('where')->renderLabel());
        $this->updateItem('{INPUT_MOVE_FIELD}', $formMove->getControl('where')->renderInput());
        $this->updateItem('{INPUT_MOVE_SUBMIT}', $formMove->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_MOVE_RESET}', $formMove->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_MOVE_END}', $formMove->renderEnd());
        $this->updateItem('{FORM_PRIMARY_START}', $formPrimary->renderStart());
        $this->updateItem('{INPUT_PRIMARY_SUBMIT}', $formPrimary->getControl('selectFile')->renderInput());
        $this->updateItem('{FORM_PRIMARY_END}', $formPrimary->renderEnd());
        $this->updateItem('{FORM_DELETE_START}', $formDelete->renderStart());
        $this->updateItem('{INPUT_DELETE_SUBMIT}', $formDelete->getControl('removeFile')->renderInput());
        $this->updateItem('{FORM_DELETE_END}', $formDelete->renderEnd());
        return $this;
    }
}
