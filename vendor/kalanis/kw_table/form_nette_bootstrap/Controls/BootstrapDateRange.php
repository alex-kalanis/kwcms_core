<?php

namespace kalanis\kw_table\form_nette_bootstrap\Controls;


use kalanis\kw_table\form_nette\Controls\DateRange;
use Nette\Forms\Container;
use Nette\Utils\Html;


class BootstrapDateRange extends DateRange
{
    protected string $inputFormat = 'd.m.Y';
    protected string $searchFormat = 'Y-m-d 00:00:00';

    protected function setControlHtml(string $name)
    {
        $span = Html::el('span');
        $divSince = Html::el('div', ['class' => "input-group dateTimePickerRange"]);
        $divTo = clone $divSince;

//        $divGroup = Html::el('div', ['class' => "input-group-addon"]);
        $start = $this->start = Html::el('input', [
            'type'        => 'text',
            'name'        => $name . '[]',
            'placeholder' => _('From'),
            'id'          => $name . 'StartId',
            'class'       => 'form-control cleanable listingDateTimePicker',
            'aria-label'  => _('Time from')
        ]);
        $end = $this->end = Html::el('input', [
            'type'        => 'text',
            'name'        => $name . '[]',
            'placeholder' => _('To'),
            'id'          => $name . 'EndId',
            'class'       => 'form-control cleanable listingDateTimePicker',
            'aria-label'  => _('Time to')
        ]);

        $divGroupBtnSearch = Html::el('div', ['class' => "input-group-btn"]);
        $divGroupBtnClear = clone $divGroupBtnSearch;
        $buttonSearch = Html::el('button', ['type' => "button", 'class' => "btn btn-default listingSearch", 'aria-label' => "Search"]);
        $buttonClear = Html::el('button', ['type' => "button", 'class' => "btn btn-default listingClear", 'aria-label' => "Clear"]);
        $spanSearch = Html::el('span', ['class' => "glyphicon glyphicon-search"]);
        $spanClear = Html::el('span', ['class' => "glyphicon glyphicon-remove"]);

        $buttonSearch->add($spanSearch);
        $buttonClear->add($spanClear);

        $divGroupBtnSearch->add($buttonSearch);
        $divGroupBtnClear->add($buttonClear);

        $divSince->add($divGroupBtnSearch);
        $divSince->add($start);
        $divSince->add($divGroupBtnClear);

        $divTo->add($divGroupBtnSearch);
        $divTo->add($end);
        $divTo->add($divGroupBtnClear);
        $span->add($divSince);
        $span->add($divTo);
        $this->control = $span;
    }

    public static function register(?string $inputFormat = null, ?string $searchFormat = null)
    {
        Container::extensionMethod('addBootstrapDateRange', function ($container, $name, $label = null, $maxLength = null) use ($inputFormat, $searchFormat) {
            $picker = $container[$name] = new BootstrapDateRange($name, $label, $maxLength);

            if (null !== $inputFormat) {
                $picker->setInputFormat($inputFormat);
            }
            if (null !== $searchFormat) {
                $picker->setSearchFormat($searchFormat);
            }

            return $picker;
        });
    }
}
