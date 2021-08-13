<?php

namespace kalanis\kw_forms\Controls\Security\Captcha;


use kalanis\kw_rules\Interfaces\IRules;

/**
 * The NOCAPTCHA server URL's
 */
define("NOCAPTCHA_API_SERVER", "https://www.google.com/recaptcha/api.js");
define("NOCAPTCHA_API_SECURE_SERVER", "https://www.google.com/recaptcha/api/siteverify");


/**
 * Class NoCaptcha
 * @package kalanis\kw_forms\Controls\Security\Captcha
 * Define NoCaptcha service to render captcha
 * Uses service from Google to check if response contains correct answer
 * @codeCoverageIgnore remote service
 */
class NoCaptcha extends ACaptcha
{
    /** @var string */
    protected static $publicKey = '';
    /** @var string */
    protected static $privateKey = '';

    public static function init(string $privateKey, string $publicKey): void
    {
        static::$publicKey = $publicKey;
        static::$privateKey = $privateKey;
    }

    public function set(string $alias, string $errorMessage): self
    {
        $this->setEntry($alias);
        parent::addRule(IRules::SATISFIES_CALLBACK, $errorMessage, [$this, 'checkNoCaptcha']);
        return $this;
    }

    public function addRule(string $ruleName, string $errorText, ...$args): void
    {
        // no additional rules applicable
    }

    public function checkNoCaptcha($value): bool
    {
        // entry has key: g-recaptcha-response
        $response = file_get_contents(NOCAPTCHA_API_SECURE_SERVER . "?secret=" . static::$privateKey . "&response=" . $value);
        $responseStructure = json_decode($response, true);
        return !empty($responseStructure["success"]) && $responseStructure["success"] === true;
    }

    public function renderInput($attributes = null): string
    {
        return $this->canPass() ? '' : $this->getHtml();
    }

    /**
     * Gets the challenge HTML (javascript only version).
     * This is called from the browser, and the resulting NoReCAPTCHA HTML widget
     * is embedded within the HTML form it was called from.
     *
     * @return string - The HTML to be embedded in the user's form.
     */
    protected function getHtml(): string
    {
        return '<script src="' . NOCAPTCHA_API_SERVER . '"></script>
	<div class="g-recaptcha" data-sitekey="' . static::$publicKey. '"></div>';
    }
}
