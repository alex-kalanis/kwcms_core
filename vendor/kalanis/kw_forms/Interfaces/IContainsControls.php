<?php

namespace kalanis\kw_forms\Interfaces;


use kalanis\kw_forms\Controls\AControl;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Validate;
use kalanis\kw_templates\Interfaces\IHtmlElement;


/**
 * Interface IContainsControls
 * @package kalanis\kw_forms\Interfaces
 * When control itself contains other controls
 */
interface IContainsControls extends IMultiValue
{
    public function hasControl(string $key): bool;

    public function getControl(string $key): ?AControl;

    /**
     * @return string[]
     */
    public function getLabels(): array;

    public function setLabels(array $array = []): void;

    /**
     * @param string[] $passedErrors
     * @param string|string[]|IHtmlElement|IHtmlElement[] $wrappersError
     * @return array
     * @throws RenderException
     */
    public function getErrors(array $passedErrors, array $wrappersError): array;

    /**
     * @param Validate $validate
     * @return bool
     */
    public function validateControls(Validate $validate): bool;

    /**
     * @return RuleException[][]
     */
    public function getValidatedErrors(): array;
}
