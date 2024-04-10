<?php

namespace kalanis\kw_forms\Controls;


use kalanis\kw_forms\Exceptions\FormsException;
use ReflectionClass;
use ReflectionException;


class Factory
{
    /** @var array<string, string> */
    protected static array $map = [
        'input' => Input::class,
        'text' => Text::class,
        'textarea' => Textarea::class,
        'email' => Email::class,
        'pass' => Password::class,
        'password' => Password::class,
        'phone' => Telephone::class,
        'telephone' => Telephone::class,
        'chk' => Checkbox::class,
        'check' => Checkbox::class,
        'checkbox' => Checkbox::class,
        'checkboxswitch' => CheckboxSwitch::class,
        'select' => Select::class,
        'selectbox' => Select::class,
        'radio' => Radio::class,
        'radioset' => RadioSet::class,
        'radiobutton' => Radio::class,
        'hidden' => Hidden::class,
        'date' => DatePicker::class,
        'datetime' => DateTimePicker::class,
        'daterange' => DateRange::class,
        'description' => Description::class,
        'desc' => Description::class,
        'html' => Html::class,
        'file' => File::class,
        'button' => Button::class,
        'accept' => Submit::class,
        'submit' => Submit::class,
        'cancel' => Reset::class,
        'reset' => Reset::class,
        'captchadis' => Security\Captcha\Disabled::class,
        'captchatext' => Security\Captcha\Text::class,
        'captchaplus' => Security\Captcha\Numerical::class,
        'nocaptcha' => Security\Captcha\NoCaptcha::class,
        'csrf' => Security\Csrf::class,
        'multisend' => Security\MultiSend::class,
    ];

    /**
     * Factory for getting classes of each input available by kw_forms
     * @param string $type
     * @throws FormsException
     * @return AControl
     */
    public function getControl(string $type): AControl
    {
        $type = strtolower($type);
        if (isset(static::$map[$type])) {
            $control = static::$map[$type];
            try {
                /** @var class-string $control */
                $ref = new ReflectionClass($control);
                $class = $ref->newInstance();
                if ($class instanceof AControl) {
                    return $class;
                }
            } catch (ReflectionException $ex) {
                throw new FormsException($ex->getMessage(), $ex->getCode(), $ex);
            }

        }
        throw new FormsException(sprintf('Unknown type %s ', $type));
    }
}
