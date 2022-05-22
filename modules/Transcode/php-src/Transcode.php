<?php

namespace KWCMS\modules\Transcode;


use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output;


class Transcode extends AModule
{
    /** @var Lib\VariantFactory */
    protected $libFactory = null;
    /** @var Lib\AVariant|null */
    protected $libVariant = null;
    /** @var Lib\MessageForm */
    protected $form = [];
    /** @var string[] */
    protected $conf = [];

    public function __construct()
    {
        $this->form = new Lib\MessageForm();
        $this->conf = [
            'site_name' => 'KWCMS3 Char Translator',
            'encoding' => 'utf-8',
        ];
        $this->libFactory = new Lib\VariantFactory();
    }

    public function process(): void
    {
        $what = $this->inputs->getInArray('what');
        if (!empty($what) && $this->libFactory->isAvailable(strval(reset($what)))) {
            $this->libVariant = $this->libFactory->getVariant(strval(reset($what)));
            $this->form->composeForm();
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process() && $this->libVariant) {
                $response = $this->form->getValue('data');

                if ('straight' == $this->form->getValue('direction')) {
                    $response = strtr($response, $this->getMappedFrom());
                    $response = strtr($response, $this->libVariant->leftoversTo());
                } else {
                    $response = strtr($response, $this->libVariant->specials());
                    $response = strtr($response, $this->getMappedTo());
                    $response = strtr($response, $this->libVariant->leftoversFrom());
                }
                $this->form->setValue('data', $response);
            }
        }
    }

    public function output(): Output\AOutput
    {
        $out = new Output\Raw();
        $defaultTemplate = new Templates\MainTemplate();
        $defaultTemplate->change('{NAME}', $this->conf['site_name']);
        $defaultTemplate->change('{CONTENT}', $this->getContent());
        $defaultTemplate->change('{TITLE}', $this->conf['site_name']);
        $defaultTemplate->change('{ENCODING}', $this->conf['encoding']);
        return $out->setContent($defaultTemplate->render());
    }

    protected function getContent(): string
    {
        $what = $this->inputs->getInArray('what');
        return empty($what) || empty($this->libVariant)
            ? $this->getSelector()
            : $this->getForm()
        ;
    }

    protected function getSelector(): string
    {
        $r = "";
        $indexInfo = new Templates\IndexMenuTemplate();
        $linksInfo = new Templates\IndexLinkTemplate();
        foreach ($this->libFactory->getList() as $name) {
            $linksInfo->reset();
            $linksInfo->change('{ADDR}', '/?what=' . $name);
            $linksInfo->change('{NAME}', $name);
            $r .= $linksInfo->render();
        }
        $indexInfo->change('{LINKS}', $r);
        return $indexInfo->render();
    }

    protected function getForm()
    {
        $indexInfo = new Templates\FormTemplate();
        $indexInfo->change('{FORM_ITSELF}', $this->form->render());
        $indexInfo->change('{FROM_BUTTONS}', $this->getCodingButtons($this->getMappedFrom()));
        $indexInfo->change('{TO_BUTTONS}', $this->getCodingButtons($this->getMappedTo()));
        $indexInfo->change('{RM_BUTTON}', $this->getRemoveButton());
        return $indexInfo->render();
    }

    protected function getCodingButtons($letters)
    {
        $buttons = new Templates\AddButtonTemplate();
        $r = '';
        foreach ($letters as $key => $letter) {
            $buttons->reset();
            $buttons->change('{LETTER}', $letter);
            $buttons->change('{ESC_LETTER}', strval($letter));
            $r .= $buttons->render();
        }
        return $r;
    }

    protected function getRemoveButton()
    {
        $button = new Templates\RmButtonTemplate();
        $button->change('{LETTER}', '&lt--');
        $button->change('{ALLOWED}', $this->libVariant->getAllowed());
        return $button->render();
    }

    public function getMappedFrom(): array
    {
        return array_map([$this, 'getMapped'], $this->libVariant->getFrom());
    }

    public function getMappedTo(): array
    {
        return array_map([$this, 'getMapped'], $this->libVariant->getTo());
    }

    public function getMapped($input): string
    {
        return $input . $this->libVariant->getSeparator();
    }
}
